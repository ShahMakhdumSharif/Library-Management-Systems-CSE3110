<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OracleDatabaseTest extends TestCase
{
    public function test_application_uses_only_the_oracle_connection(): void
    {
        $this->assertSame('oracle', config('database.default'));
        $this->assertSame(['oracle'], array_keys(config('database.connections')));
        $this->assertSame(['database'], array_keys(config('cache.stores')));
        $this->assertSame(['database'], array_keys(config('queue.connections')));
        $this->assertSame('oracle', config('session.connection'));
        $this->assertSame('oracle', DB::connection()->getDriverName());
    }

    public function test_required_oracle_tables_exist(): void
    {
        $requiredTables = [
            'ROLES',
            'USERS',
            'SESSIONS',
            'CACHE',
            'CACHE_LOCKS',
            'JOBS',
            'JOB_BATCHES',
            'FAILED_JOBS',
            'NOTIFICATIONS',
            'BRANCHES',
            'BOOKS',
            'BOOK_DETAILS',
            'BOOK_COPIES',
            'ISSUE_TRANSACTIONS',
            'RESERVATIONS',
            'FINES',
        ];

        $foundTables = collect(DB::select(
            "SELECT table_name FROM user_tables WHERE table_name IN ('".implode("','", $requiredTables)."')"
        ))->pluck('table_name')->sort()->values()->all();

        sort($requiredTables);
        $this->assertSame($requiredTables, $foundTables);
        $migrationTable = DB::selectOne(
            "SELECT COUNT(*) AS total FROM user_tables WHERE table_name = 'MIGRATIONS'"
        );
        $this->assertSame(0, (int) $migrationTable->total);
    }

    public function test_oracle_plsql_objects_are_valid(): void
    {
        $invalidObjects = DB::select("
            SELECT object_name, object_type
            FROM user_objects
            WHERE status = 'INVALID'
              AND object_name IN (
                'CALCULATE_FINE',
                'ISSUE_BOOK',
                'RETURN_BOOK',
                'CREATE_OVERDUE_NOTICES',
                'INVENTORY_REPORT',
                'OVERDUE_REPORT'
              )
        ");

        $this->assertSame([], $invalidObjects);
    }

    public function test_oracle_fine_function_returns_expected_amount(): void
    {
        $result = DB::selectOne("
            SELECT calculate_fine(
                TO_DATE('01-JUL-2026', 'DD-MON-YYYY'),
                TO_DATE('04-JUL-2026', 'DD-MON-YYYY')
            ) AS amount
            FROM dual
        ");

        $this->assertSame(15, (int) $result->amount);
    }

    public function test_application_reads_catalog_and_authenticates_from_oracle(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Database System Concepts');

        $this->post('/login', [
            'email' => 'member@library.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get('/books')
            ->assertOk()
            ->assertSee('Database System Concepts');
    }
}
