<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LibraryController extends Controller
{
    private const FINE_PER_DAY = 5;

    public function home(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $sort = $this->normalizedBookSort((string) $request->query('sort', 'title'));
        $books = collect();

        if ($query !== '') {
            $books = $this->applyBookSort(
                $this->applyBookFilters($this->bookQuery(), $query),
                $sort
            )
                ->limit(12)
                ->get();
        }

        return view('welcome', [
            'books' => $books,
            'query' => $query,
            'sort' => $sort,
            'featured' => $this->bookQuery()->orderBy('books.id')->limit(3)->get(),
        ]);
    }

    public function dashboard(): View
    {
        $user = Auth::user();

        return view('dashboard', [
            'role' => $user->roleName(),
            'stats' => [
                'titles' => DB::table('books')->count(),
                'available' => DB::table('book_copies')->where('status', 'available')->count(),
                'on_loan' => DB::table('issue_transactions')->where('status', 'issued')->count(),
                'reservations' => $user->isStaff()
                    ? DB::table('reservations')->where('status', 'waiting')->count()
                    : DB::table('reservations')->where('user_id', $user->id)->where('status', 'waiting')->count(),
            ],
            'recentLoans' => $this->loanQuery()
                ->when(! $user->isStaff(), fn ($query) => $query->where('issue_transactions.user_id', $user->id))
                ->orderByDesc('issue_transactions.id')
                ->limit(5)
                ->get(),
        ]);
    }

    public function books(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $sort = $this->normalizedBookSort((string) $request->query('sort', 'title'));
        $books = $this->applyBookSort(
            $this->applyBookFilters($this->bookQuery(), $search),
            $sort
        )
            ->paginate(12)
            ->withQueryString();

        return view('books.index', compact('books', 'search', 'sort'));
    }

    public function createBook(): View
    {
        $this->staffOnly();

        return view('books.form', ['book' => null, 'details' => null, 'branches' => DB::table('branches')->orderBy('name')->get()]);
    }

    public function storeBook(Request $request): RedirectResponse
    {
        $this->staffOnly();
        $data = $this->validateBook($request);

        DB::transaction(function () use ($data) {
            $bookId = DB::table('books')->insertGetId([
                'title' => $data['title'],
                'isbn' => $data['isbn'],
                'publisher' => $data['publisher'],
                'published_year' => $data['published_year'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
            DB::table('book_details')->insert(['book_id' => $bookId, 'author' => $data['author'], 'category' => $data['category']]);
            $this->createCopies($bookId, (int) $data['branch_id'], (int) $data['copy_count']);
        });

        return redirect()->route('books.index')->with('success', 'Book and copies added.');
    }

    public function editBook(int $id): View
    {
        $this->staffOnly();
        $book = DB::table('books')->where('id', $id)->first();
        abort_unless($book, 404);

        return view('books.form', [
            'book' => $book,
            'details' => DB::table('book_details')->where('book_id', $id)->first(),
            'branches' => DB::table('branches')->orderBy('name')->get(),
        ]);
    }

    public function updateBook(Request $request, int $id): RedirectResponse
    {
        $this->staffOnly();
        abort_unless(DB::table('books')->where('id', $id)->exists(), 404);
        $data = $this->validateBook($request, $id, false);

        DB::transaction(function () use ($data, $id) {
            DB::table('books')->where('id', $id)->update([
                'title' => $data['title'],
                'isbn' => $data['isbn'],
                'publisher' => $data['publisher'],
                'published_year' => $data['published_year'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
            DB::table('book_details')->where('book_id', $id)->update(['author' => $data['author'], 'category' => $data['category']]);
            if ((int) ($data['copy_count'] ?? 0) > 0) {
                $this->createCopies($id, (int) $data['branch_id'], (int) $data['copy_count']);
            }
        });

        return redirect()->route('books.index')->with('success', 'Book updated.');
    }

    public function deleteBook(int $id): RedirectResponse
    {
        $this->staffOnly();
        $hasHistory = DB::table('issue_transactions')
            ->join('book_copies', 'book_copies.id', '=', 'issue_transactions.copy_id')
            ->where('book_copies.book_id', $id)->exists();
        if ($hasHistory) {
            return back()->with('error', 'This title has circulation history and cannot be deleted.');
        }
        DB::table('books')->where('id', $id)->delete();

        return back()->with('success', 'Book removed.');
    }

    public function reserve(int $bookId): RedirectResponse
    {
        abort_unless(DB::table('books')->where('id', $bookId)->exists(), 404);
        $exists = DB::table('reservations')->where('user_id', Auth::id())->where('book_id', $bookId)->where('status', 'waiting')->exists();
        if (! $exists) {
            DB::table('reservations')->insert([
                'user_id' => Auth::id(),
                'book_id' => $bookId,
                'status' => 'waiting',
                'reserved_at' => now(),
            ]);
            $this->notify(Auth::id(), 'reservation', 'Your reservation has been added to the queue.');
        }

        return back()->with('success', $exists ? 'You are already in this reservation queue.' : 'Book reserved.');
    }

    public function circulation(): View
    {
        $this->staffOnly();

        return view('circulation.index', [
            'loans' => $this->loanQuery()->orderByDesc('issue_transactions.id')->paginate(15),
            'members' => $this->membersQuery()->where('users.status', 'active')->orderBy('users.name')->get(),
            'copies' => DB::table('book_copies')
                ->join('books', 'books.id', '=', 'book_copies.book_id')
                ->join('branches', 'branches.id', '=', 'book_copies.branch_id')
                ->where('book_copies.status', 'available')
                ->orderBy('books.title')
                ->select('book_copies.id', 'book_copies.barcode', 'books.title', 'branches.name as branch')->get(),
        ]);
    }

    public function issue(Request $request): RedirectResponse
    {
        $this->staffOnly();
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'copy_id' => ['required', 'integer', 'exists:book_copies,id'],
        ]);
        $copy = DB::table('book_copies')->where('id', $data['copy_id'])->first();
        if (! $copy || $copy->status !== 'available') {
            return back()->with('error', 'That copy is no longer available.');
        }

        DB::transaction(function () use ($data) {
            DB::table('issue_transactions')->insert([
                'user_id' => $data['user_id'],
                'copy_id' => $data['copy_id'],
                'issue_date' => today(),
                'due_date' => today()->addDays(14),
                'status' => 'issued',
            ]);
            DB::table('book_copies')->where('id', $data['copy_id'])->update(['status' => 'issued']);
            $this->notify($data['user_id'], 'loan', 'A book was issued to you. It is due in 14 days.');
        });

        return back()->with('success', 'Book issued successfully.');
    }

    public function returnBook(int $transactionId): RedirectResponse
    {
        $this->staffOnly();
        $loan = DB::table('issue_transactions')->where('id', $transactionId)->first();
        abort_unless($loan, 404);
        if ($loan->status !== 'issued') {
            return back()->with('error', 'This loan is already closed.');
        }

        DB::transaction(function () use ($loan) {
            $today = today();
            DB::table('issue_transactions')->where('id', $loan->id)->update(['return_date' => $today, 'status' => 'returned']);
            DB::table('book_copies')->where('id', $loan->copy_id)->update(['status' => 'available']);
            $daysLate = max(0, Carbon::parse($loan->due_date)->startOfDay()->diffInDays($today, false));
            if ($daysLate > 0) {
                $amount = $daysLate * self::FINE_PER_DAY;
                DB::table('fines')->insert([
                    'user_id' => $loan->user_id,
                    'transaction_id' => $loan->id,
                    'amount' => $amount,
                    'status' => 'unpaid',
                ]);
                $this->notify($loan->user_id, 'fine', "A late-return fine of Tk {$amount} was added.");
            } else {
                $this->notify($loan->user_id, 'return', 'Your returned book was received. Thank you.');
            }
        });

        return back()->with('success', 'Return recorded and any late fine calculated.');
    }

    public function reservations(): View
    {
        $query = DB::table('reservations')
            ->join('users', 'users.id', '=', 'reservations.user_id')
            ->join('books', 'books.id', '=', 'reservations.book_id')
            ->select('reservations.*', 'users.name as member', 'books.title');
        if (! Auth::user()->isStaff()) {
            $query->where('reservations.user_id', Auth::id());
        }

        return view('reservations.index', ['reservations' => $query->orderByDesc('reserved_at')->paginate(15)]);
    }

    public function completeReservation(int $id): RedirectResponse
    {
        $this->staffOnly();
        $reservation = DB::table('reservations')->where('id', $id)->first();
        abort_unless($reservation, 404);
        DB::table('reservations')->where('id', $id)->update(['status' => 'notified', 'fulfilled_at' => now()]);
        $this->notify($reservation->user_id, 'reservation', 'A copy of your reserved book is ready. Please visit the library desk.');

        return back()->with('success', 'Member notified.');
    }

    public function fines(): View
    {
        $query = DB::table('fines')
            ->join('users', 'users.id', '=', 'fines.user_id')
            ->join('issue_transactions', 'issue_transactions.id', '=', 'fines.transaction_id')
            ->join('book_copies', 'book_copies.id', '=', 'issue_transactions.copy_id')
            ->join('books', 'books.id', '=', 'book_copies.book_id')
            ->select('fines.*', 'users.name as member', 'books.title');
        if (! Auth::user()->isStaff()) {
            $query->where('fines.user_id', Auth::id());
        }

        return view('fines.index', ['fines' => $query->orderByDesc('fines.id')->paginate(15)]);
    }

    public function payFine(int $id): RedirectResponse
    {
        $fine = DB::table('fines')->where('id', $id)->first();
        abort_unless($fine, 404);
        abort_unless(Auth::user()->isStaff() || $fine->user_id === Auth::id(), 403);
        DB::table('fines')->where('id', $id)->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Fine marked as paid.');
    }

    public function members(): View
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        return view('members.index', [
            'members' => $this->membersQuery()->orderBy('users.name')->paginate(15),
            'roles' => DB::table('roles')->orderBy('id')->get(),
        ]);
    }

    public function updateMember(Request $request, int $id): RedirectResponse
    {
        abort_unless(Auth::user()->isAdmin(), 403);
        $data = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['required', 'in:active,suspended'],
        ]);
        abort_if($id === Auth::id() && $data['status'] !== 'active', 422, 'You cannot suspend your own account.');
        DB::table('users')->where('id', $id)->update($data);

        return back()->with('success', 'Member access updated.');
    }

    public function notifications(): View
    {
        return view('notifications.index', [
            'notifications' => DB::table('notifications')->where('user_id', Auth::id())->orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function readNotification(int $id): RedirectResponse
    {
        DB::table('notifications')->where('id', $id)->where('user_id', Auth::id())->update(['is_read' => true]);

        return back();
    }

    public function reports(): View
    {
        $this->staffOnly();

        return view('reports.index', [
            'inventory' => DB::table('books')
                ->leftJoin('book_copies', 'book_copies.book_id', '=', 'books.id')
                ->select('books.title', DB::raw('COUNT(book_copies.id) as total_copies'), DB::raw("SUM(CASE WHEN book_copies.status = 'available' THEN 1 ELSE 0 END) as available_copies"))
                ->groupBy('books.id', 'books.title')->orderBy('books.title')->get(),
            'overdue' => $this->loanQuery()->where('issue_transactions.status', 'issued')
                ->whereDate('issue_transactions.due_date', '<', today())->orderBy('issue_transactions.due_date')->get(),
            'fineTotal' => DB::table('fines')->where('status', 'unpaid')->sum('amount'),
        ]);
    }

    private function validateBook(Request $request, ?int $id = null, bool $copiesRequired = true): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:32', 'unique:books,isbn'.($id ? ",{$id}" : '')],
            'publisher' => ['required', 'string', 'max:255'],
            'published_year' => ['nullable', 'integer', 'between:1000,'.date('Y')],
            'description' => ['nullable', 'string', 'max:2000'],
            'author' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'copy_count' => [$copiesRequired ? 'required' : 'nullable', 'integer', 'between:0,50'],
        ]);
    }

    private function createCopies(int $bookId, int $branchId, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $id = ((int) DB::table('book_copies')->max('id')) + 1;
            DB::table('book_copies')->insert([
                'book_id' => $bookId,
                'branch_id' => $branchId,
                'barcode' => 'CL-'.str_pad((string) $id, 6, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }
    }

    private function notify(int $userId, string $type, string $message): void
    {
        DB::table('notifications')->insert([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'is_read' => false,
            'created_at' => now(),
        ]);
    }

    private function staffOnly(): void
    {
        abort_unless(Auth::user()?->isStaff(), 403);
    }

    private function bookQuery()
    {
        return DB::table('books')
            ->join('book_details', 'book_details.book_id', '=', 'books.id')
            ->select(
                'books.*',
                'book_details.author',
                'book_details.category',
                DB::raw('(SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = books.id) AS total_copies'),
                DB::raw("(SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = books.id AND bc.status = 'available') AS available_copies")
            );
    }

    private function applyBookFilters($query, string $search)
    {
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $term = '%'.strtolower($search).'%';
                $builder->whereRaw('LOWER(title) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(author) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$term])
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function normalizedBookSort(string $sort): string
    {
        return in_array($sort, ['title', 'publisher', 'category', 'author', 'isbn'], true) ? $sort : 'title';
    }

    private function applyBookSort($query, string $sort)
    {
        return match ($sort) {
            'publisher' => $query->orderByRaw('LOWER(books.publisher)')
                ->orderByRaw('LOWER(books.title)')
                ->orderBy('books.id'),
            'category' => $query->orderByRaw('LOWER(book_details.category)')
                ->orderByRaw('LOWER(books.title)')
                ->orderBy('books.id'),
            'author' => $query->orderByRaw('LOWER(book_details.author)')
                ->orderByRaw('LOWER(books.title)')
                ->orderBy('books.id'),
            'isbn' => $query->orderByRaw('LOWER(books.isbn)')
                ->orderByRaw('LOWER(books.title)')
                ->orderBy('books.id'),
            default => $query->orderByRaw('LOWER(books.title)')->orderBy('books.id'),
        };
    }

    private function loanQuery()
    {
        return DB::table('issue_transactions')
            ->join('users', 'users.id', '=', 'issue_transactions.user_id')
            ->join('book_copies', 'book_copies.id', '=', 'issue_transactions.copy_id')
            ->join('books', 'books.id', '=', 'book_copies.book_id')
            ->select('issue_transactions.*', 'users.name as member', 'books.title', 'book_copies.barcode');
    }

    private function membersQuery()
    {
        return DB::table('users')->join('roles', 'roles.id', '=', 'users.role_id')->select('users.*', 'roles.role_name');
    }
}
