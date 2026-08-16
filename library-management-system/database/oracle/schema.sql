-- CENTRAL LIBRARY MANAGEMENT SYSTEM
-- sqlplus library_user/library_password@localhost:1521/XEPDB1 @database/oracle/schema.sql

SET SERVEROUTPUT ON;
SET DEFINE OFF;

-- TABLES: relational design, keys and integrity constraints

CREATE TABLE roles (
    id NUMBER(19) NOT NULL,
    role_name VARCHAR2(40) NOT NULL,
    CONSTRAINT roles_pk PRIMARY KEY (id),
    CONSTRAINT roles_name_uk UNIQUE (role_name)
);

CREATE TABLE users (
    id NUMBER(19) NOT NULL,
    name VARCHAR2(255) NOT NULL,
    email VARCHAR2(255) NOT NULL,
    password VARCHAR2(255) NOT NULL,
    role_id NUMBER(19) NOT NULL,
    status VARCHAR2(20) DEFAULT 'active' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (id),
    CONSTRAINT users_email_uk UNIQUE (email),
    CONSTRAINT users_role_fk FOREIGN KEY (role_id) REFERENCES roles (id),
    CONSTRAINT users_status_ck CHECK (status IN ('active', 'suspended'))
);

CREATE TABLE sessions (
    id VARCHAR2(255) NOT NULL,
    user_id NUMBER(19),
    ip_address VARCHAR2(45),
    user_agent CLOB,
    payload CLOB NOT NULL,
    last_activity NUMBER(10) NOT NULL,
    CONSTRAINT sessions_pk PRIMARY KEY (id),
    CONSTRAINT sessions_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id NUMBER(19) NOT NULL,
    user_id NUMBER(19) NOT NULL,
    type VARCHAR2(40) NOT NULL,
    message CLOB NOT NULL,
    is_read NUMBER(1) DEFAULT 0 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT notifications_pk PRIMARY KEY (id),
    CONSTRAINT notifications_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT notifications_read_ck CHECK (is_read IN (0, 1))
);

CREATE TABLE branches (
    id NUMBER(19) NOT NULL,
    name VARCHAR2(255) NOT NULL,
    location CLOB NOT NULL,
    hours VARCHAR2(100),
    CONSTRAINT branches_pk PRIMARY KEY (id),
    CONSTRAINT branches_name_uk UNIQUE (name)
);

CREATE TABLE books (
    id NUMBER(19) NOT NULL,
    title VARCHAR2(255) NOT NULL,
    isbn VARCHAR2(32) NOT NULL,
    publisher VARCHAR2(255) NOT NULL,
    published_year NUMBER(4),
    description CLOB,
    CONSTRAINT books_pk PRIMARY KEY (id),
    CONSTRAINT books_isbn_uk UNIQUE (isbn),
    CONSTRAINT books_year_ck CHECK (published_year BETWEEN 1000 AND 9999)
);

CREATE TABLE book_details (
    id NUMBER(19) NOT NULL,
    book_id NUMBER(19) NOT NULL,
    author VARCHAR2(255) NOT NULL,
    category VARCHAR2(255) NOT NULL,
    CONSTRAINT book_details_pk PRIMARY KEY (id),
    CONSTRAINT book_details_book_uk UNIQUE (book_id),
    CONSTRAINT book_details_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE
);

CREATE TABLE book_copies (
    id NUMBER(19) NOT NULL,
    book_id NUMBER(19) NOT NULL,
    branch_id NUMBER(19) NOT NULL,
    barcode VARCHAR2(40) NOT NULL,
    status VARCHAR2(20) DEFAULT 'available' NOT NULL,
    CONSTRAINT book_copies_pk PRIMARY KEY (id),
    CONSTRAINT copies_barcode_uk UNIQUE (barcode),
    CONSTRAINT copies_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    CONSTRAINT copies_branch_fk FOREIGN KEY (branch_id) REFERENCES branches (id) ON DELETE CASCADE,
    CONSTRAINT copies_status_ck CHECK (status IN ('available', 'issued', 'maintenance'))
);

CREATE TABLE issue_transactions (
    id NUMBER(19) NOT NULL,
    user_id NUMBER(19) NOT NULL,
    copy_id NUMBER(19) NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    status VARCHAR2(20) DEFAULT 'issued' NOT NULL,
    CONSTRAINT issues_pk PRIMARY KEY (id),
    CONSTRAINT issues_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT issues_copy_fk FOREIGN KEY (copy_id) REFERENCES book_copies (id) ON DELETE CASCADE,
    CONSTRAINT issues_status_ck CHECK (status IN ('issued', 'returned'))
);

CREATE TABLE reservations (
    id NUMBER(19) NOT NULL,
    user_id NUMBER(19) NOT NULL,
    book_id NUMBER(19) NOT NULL,
    status VARCHAR2(20) DEFAULT 'waiting' NOT NULL,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    fulfilled_at TIMESTAMP,
    CONSTRAINT reservations_pk PRIMARY KEY (id),
    CONSTRAINT reservations_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT reservations_book_fk FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    CONSTRAINT reservations_status_ck CHECK (status IN ('waiting', 'notified', 'cancelled'))
);

CREATE TABLE fines (
    id NUMBER(19) NOT NULL,
    user_id NUMBER(19) NOT NULL,
    transaction_id NUMBER(19) NOT NULL,
    amount NUMBER(10, 2) NOT NULL,
    status VARCHAR2(20) DEFAULT 'unpaid' NOT NULL,
    paid_at TIMESTAMP,
    CONSTRAINT fines_pk PRIMARY KEY (id),
    CONSTRAINT fines_transaction_uk UNIQUE (transaction_id),
    CONSTRAINT fines_user_fk FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fines_issue_fk FOREIGN KEY (transaction_id) REFERENCES issue_transactions (id) ON DELETE CASCADE,
    CONSTRAINT fines_amount_ck CHECK (amount >= 0),
    CONSTRAINT fines_status_ck CHECK (status IN ('unpaid', 'paid'))
);

CREATE INDEX sessions_user_idx ON sessions (user_id);
CREATE INDEX sessions_activity_idx ON sessions (last_activity);
CREATE INDEX copies_book_idx ON book_copies (book_id);
CREATE INDEX book_details_author_idx ON book_details (author);
CREATE INDEX book_details_category_idx ON book_details (category);
CREATE INDEX issues_user_idx ON issue_transactions (user_id);
CREATE INDEX reservations_queue_idx ON reservations (book_id, status, reserved_at);

CREATE OR REPLACE VIEW book_catalog_v AS
SELECT
    books.id,
    books.title,
    books.isbn,
    books.publisher,
    books.published_year,
    books.description,
    book_details.author,
    book_details.category,
    (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = books.id) AS total_copies,
    (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id = books.id AND bc.status = 'available') AS available_copies
FROM books
JOIN book_details ON book_details.book_id = books.id;

-- SEQUENCES AND BEFORE-INSERT TRIGGERS

CREATE SEQUENCE roles_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE users_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE notifications_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE branches_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE books_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE book_details_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE book_copies_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE issues_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE reservations_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;
CREATE SEQUENCE fines_seq START WITH 1 INCREMENT BY 1 NOCACHE NOCYCLE;

CREATE OR REPLACE TRIGGER roles_bir BEFORE INSERT ON roles FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT roles_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER users_bir BEFORE INSERT ON users FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT users_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER notifications_bir BEFORE INSERT ON notifications FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT notifications_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER branches_bir BEFORE INSERT ON branches FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT branches_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER books_bir BEFORE INSERT ON books FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT books_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER book_details_bir BEFORE INSERT ON book_details FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT book_details_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER book_copies_bir BEFORE INSERT ON book_copies FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT book_copies_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER issues_bir BEFORE INSERT ON issue_transactions FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT issues_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER reservations_bir BEFORE INSERT ON reservations FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT reservations_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/
CREATE OR REPLACE TRIGGER fines_bir BEFORE INSERT ON fines FOR EACH ROW
BEGIN IF :NEW.id IS NULL THEN SELECT fines_seq.NEXTVAL INTO :NEW.id FROM dual; END IF; END;
/

-- A circulation trigger supplies the standard 14-day loan dates.
CREATE OR REPLACE TRIGGER loan_defaults_bir
BEFORE INSERT ON issue_transactions
FOR EACH ROW
BEGIN
    IF :NEW.issue_date IS NULL THEN
        :NEW.issue_date := TRUNC(SYSDATE);
    END IF;
    IF :NEW.due_date IS NULL THEN
        :NEW.due_date := :NEW.issue_date + 14;
    END IF;
    IF :NEW.status IS NULL THEN
        :NEW.status := 'issued';
    END IF;
END;
/

-- initial data (INSERT / COMMIT)
INSERT INTO roles (role_name) VALUES ('Admin');
INSERT INTO roles (role_name) VALUES ('Librarian');
INSERT INTO roles (role_name) VALUES ('Member');

INSERT INTO users (name, email, password, role_id)
VALUES ('System Administrator', 'admin@central.library',
        '$2y$10$tPciGOdm.BCV0KIlMWW9he02v1GJjs19ji173o6itnGViCYH4nhie', 1);

-- calculate Tk 5 for every overdue day

CREATE OR REPLACE FUNCTION calculate_fine (
    p_due_date DATE,
    p_return_date DATE
) RETURN NUMBER
IS
    v_days_late NUMBER;
BEGIN
    v_days_late := TRUNC(NVL(p_return_date, SYSDATE)) - TRUNC(p_due_date);
    IF v_days_late > 0 THEN
        RETURN v_days_late * 5;
    ELSE
        RETURN 0;
    END IF;
END;
/

-- issue and return workflow

CREATE OR REPLACE PROCEDURE issue_book (
    p_user_id IN NUMBER,
    p_copy_id IN NUMBER
)
IS
    v_copy_status book_copies.status%TYPE;
BEGIN
    SELECT status INTO v_copy_status
    FROM book_copies
    WHERE id = p_copy_id
    FOR UPDATE;

    IF v_copy_status <> 'available' THEN
        RAISE_APPLICATION_ERROR(-20001, 'Book copy is not available');
    END IF;

    INSERT INTO issue_transactions
        (user_id, copy_id, issue_date, due_date, status)
    VALUES
        (p_user_id, p_copy_id, TRUNC(SYSDATE), TRUNC(SYSDATE) + 14, 'issued');

    UPDATE book_copies SET status = 'issued' WHERE id = p_copy_id;
    INSERT INTO notifications (user_id, type, message)
    VALUES (p_user_id, 'loan', 'A book was issued to you and is due in 14 days.');
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20002, 'Book copy does not exist');
END;
/

CREATE OR REPLACE PROCEDURE return_book (
    p_transaction_id IN NUMBER
)
IS
    v_user_id issue_transactions.user_id%TYPE;
    v_copy_id issue_transactions.copy_id%TYPE;
    v_due_date issue_transactions.due_date%TYPE;
    v_status issue_transactions.status%TYPE;
    v_fine NUMBER(10, 2);
BEGIN
    SELECT user_id, copy_id, due_date, status
    INTO v_user_id, v_copy_id, v_due_date, v_status
    FROM issue_transactions
    WHERE id = p_transaction_id
    FOR UPDATE;

    IF v_status <> 'issued' THEN
        RAISE_APPLICATION_ERROR(-20003, 'Loan is already closed');
    END IF;

    UPDATE issue_transactions
    SET return_date = TRUNC(SYSDATE), status = 'returned'
    WHERE id = p_transaction_id;

    UPDATE book_copies SET status = 'available' WHERE id = v_copy_id;
    v_fine := calculate_fine(v_due_date, TRUNC(SYSDATE));

    IF v_fine > 0 THEN
        INSERT INTO fines (user_id, transaction_id, amount, status)
        VALUES (v_user_id, p_transaction_id, v_fine, 'unpaid');
        INSERT INTO notifications (user_id, type, message)
        VALUES (v_user_id, 'fine', 'A late-return fine was added to your account.');
    ELSE
        INSERT INTO notifications (user_id, type, message)
        VALUES (v_user_id, 'return', 'Your returned book was received. Thank you.');
    END IF;
END;
/

-- create reminders for every overdue loan

CREATE OR REPLACE PROCEDURE create_overdue_notices
IS
    CURSOR overdue_cursor IS
        SELECT t.id, t.user_id, b.title, t.due_date
        FROM issue_transactions t
        JOIN book_copies c ON c.id = t.copy_id
        JOIN books b ON b.id = c.book_id
        WHERE t.status = 'issued'
          AND t.due_date < TRUNC(SYSDATE);
BEGIN
    FOR overdue_record IN overdue_cursor LOOP
        INSERT INTO notifications (user_id, type, message)
        VALUES (
            overdue_record.user_id,
            'overdue',
            'Overdue: ' || overdue_record.title ||
            ' was due on ' || TO_CHAR(overdue_record.due_date, 'DD-MON-YYYY')
        );
    END LOOP;
END;
/

-- REPORT VIEWS(stock dekhe)

CREATE OR REPLACE VIEW inventory_report AS
SELECT
    b.id,
    b.title,
    d.author,
    d.category,
    COUNT(c.id) AS total_copies,
    SUM(CASE WHEN c.status = 'available' THEN 1 ELSE 0 END) AS available_copies
FROM books b
JOIN book_details d ON d.book_id = b.id
LEFT OUTER JOIN book_copies c ON c.book_id = b.id
GROUP BY b.id, b.title, d.author, d.category;

CREATE OR REPLACE VIEW overdue_report AS
SELECT
    t.id AS transaction_id,
    u.name AS member_name,
    u.email,
    b.title,
    c.barcode,
    t.due_date,
    TRUNC(SYSDATE) - TRUNC(t.due_date) AS days_overdue,
    calculate_fine(t.due_date, SYSDATE) AS current_fine
FROM issue_transactions t
JOIN users u ON u.id = t.user_id
JOIN book_copies c ON c.id = t.copy_id
JOIN books b ON b.id = c.book_id
WHERE t.status = 'issued'
  AND t.due_date < TRUNC(SYSDATE);
