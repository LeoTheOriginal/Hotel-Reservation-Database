-- Tworzenie schematu
CREATE SCHEMA IF NOT EXISTS rezerwacje_hotelowe;

-- Dropy istniejących wyzwalaczy, funkcji, widoków, oraz tabel
DROP TRIGGER IF EXISTS trig_aktualizuj_status_po_rezerwacji ON rezerwacje_hotelowe.rezerwacja_pokój CASCADE;
DROP TRIGGER IF EXISTS trig_aktualizuj_status_po_wymeldowaniu ON rezerwacje_hotelowe.rezerwacja_pokój CASCADE;
DROP TRIGGER IF EXISTS trig_aktualizuj_date_platnosci ON rezerwacje_hotelowe.status_płatności;


DROP FUNCTION IF EXISTS rezerwacje_hotelowe.oblicz_koszt_rezerwacji CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.przypisz_pokoj CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.sprawdz_dostepne_pokoje CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.zmien_status_pokoju CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.dodaj_rezerwacje CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.zaktualizuj_rezerwacje CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.usun_rezerwacje CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.anuluj_rezerwacje CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.wykwateruj_rezerwacje CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.aktualizuj_date_platnosci CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.aktualizuj_status_platnosci CASCADE;
DROP FUNCTION IF EXISTS rezerwacje_hotelowe.oblicz_koszt_rezerwacji_z_parametrami CASCADE;

DROP VIEW IF EXISTS rezerwacje_hotelowe.liczba_rezerwacji_gościa CASCADE;
DROP VIEW IF EXISTS rezerwacje_hotelowe.przychód_miesięczny CASCADE;
DROP VIEW IF EXISTS rezerwacje_hotelowe.przychód_miesięczny_filtr CASCADE;


DROP TABLE IF EXISTS rezerwacje_hotelowe.rezerwacja_pokój CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.pokój CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.piętro CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.status_pokoju CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.wyposażenie CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.klasa_pokoju CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.typ_łóżka CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.rezerwacja_dodatek CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.dodatek CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.rezerwacja CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.status_płatności CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.status_rezerwacji CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.gość CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.pracownik CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.rola CASCADE;
DROP TABLE IF EXISTS rezerwacje_hotelowe.logi_systemowe CASCADE;

-- Tworzenie tabel
CREATE TABLE rezerwacje_hotelowe.gość (
    id SERIAL PRIMARY KEY,
    imię VARCHAR(200) NOT NULL,
    nazwisko VARCHAR(200) NOT NULL,
    numer_telefonu VARCHAR(20) NOT NULL,
    adres_email VARCHAR(350) NOT NULL,
    CONSTRAINT unikalny_gość_email UNIQUE (adres_email),
    CONSTRAINT chk_numer_telefonu
        CHECK (numer_telefonu ~ '^^(\+[0-9]{1,3})?[0-9]{9}$')
);

CREATE TABLE rezerwacje_hotelowe.rezerwacja (
    id SERIAL PRIMARY KEY,
    gość_id INT NOT NULL,
    status_płatności_id INT NOT NULL,
    status_rezerwacji_id INT NOT NULL,
    data_zameldowania DATE NOT NULL,
    data_wymeldowania DATE NOT NULL,
    liczba_dorosłych INT CHECK (liczba_dorosłych >= 0),
    liczba_dzieci INT CHECK (liczba_dzieci >= 0),
    kwota_rezerwacji DECIMAL(10, 2) CHECK (kwota_rezerwacji >= 0),
    CONSTRAINT fk_rezerwacja_gość FOREIGN KEY (gość_id) REFERENCES rezerwacje_hotelowe.gość (id),
    CONSTRAINT fk_rezerwacja_status_płatności FOREIGN KEY (status_płatności_id) REFERENCES rezerwacje_hotelowe.status_płatności (id),
    CONSTRAINT fk_rezerwacja_status_rezerwacji FOREIGN KEY (status_rezerwacji_id) REFERENCES rezerwacje_hotelowe.status_rezerwacji (id)
);

CREATE TABLE rezerwacje_hotelowe.status_płatności (
    id SERIAL PRIMARY KEY,
    nazwa_statusu VARCHAR(50) NOT NULL UNIQUE,
    data_płatności TIMESTAMP
);

CREATE TABLE rezerwacje_hotelowe.status_rezerwacji (
    id SERIAL PRIMARY KEY,
    nazwa_statusu VARCHAR(50) NOT NULL UNIQUE
);

-- Dodanie przykładowych statusów
INSERT INTO rezerwacje_hotelowe.status_rezerwacji (nazwa_statusu) VALUES
('Oczekująca'),
('Potwierdzona'),
('W trakcie'),
('Zrealizowana'),
('Anulowana');

CREATE TABLE rezerwacje_hotelowe.dodatek (
    id SERIAL PRIMARY KEY,
    nazwa_dodatku VARCHAR(100) NOT NULL,
    cena DECIMAL(10, 2) CHECK (cena >= 0)
);

CREATE TABLE rezerwacje_hotelowe.rezerwacja_dodatek (
    rezerwacja_id INT NOT NULL,
    dodatek_id INT NOT NULL,
    PRIMARY KEY (rezerwacja_id, dodatek_id),
    CONSTRAINT fk_rez_dod_rezerwacja FOREIGN KEY (rezerwacja_id) REFERENCES rezerwacje_hotelowe.rezerwacja (id),
    CONSTRAINT fk_rez_dod_dodatek FOREIGN KEY (dodatek_id) REFERENCES rezerwacje_hotelowe.dodatek (id)
);

CREATE TABLE rezerwacje_hotelowe.typ_łóżka (
    id SERIAL PRIMARY KEY,
    nazwa_typu VARCHAR(50) NOT NULL,
    liczba_osob INT NOT NULL DEFAULT 1
);

CREATE TABLE rezerwacje_hotelowe.klasa_pokoju (
    id SERIAL PRIMARY KEY,
    nazwa_klasy VARCHAR(100) NOT NULL,
    cena_podstawowa DECIMAL(10, 2) CHECK (cena_podstawowa >= 0)
);

CREATE TABLE rezerwacje_hotelowe.wyposażenie (
    id SERIAL PRIMARY KEY,
    nazwa_wyposażenia VARCHAR(100) NOT NULL
);

CREATE TABLE rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy (
    klasa_pokoju_id INT NOT NULL,
    wyposażenie_id INT NOT NULL,
    PRIMARY KEY (klasa_pokoju_id, wyposażenie_id),
    CONSTRAINT fk_klasa_wyposażenie_klasa FOREIGN KEY (klasa_pokoju_id) REFERENCES rezerwacje_hotelowe.klasa_pokoju (id),
    CONSTRAINT fk_klasa_wyposażenie_wyposażenie FOREIGN KEY (wyposażenie_id) REFERENCES rezerwacje_hotelowe.wyposażenie (id)
);

CREATE TABLE rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy (
    id SERIAL PRIMARY KEY,
    klasa_pokoju_id INT NOT NULL,
    typ_łóżka_id INT NOT NULL,
    liczba_łóżek INT CHECK (liczba_łóżek > 0),
    CONSTRAINT fk_klasa_typ_łóżka_klasa FOREIGN KEY (klasa_pokoju_id) REFERENCES rezerwacje_hotelowe.klasa_pokoju (id),
    CONSTRAINT fk_klasa_typ_łóżka_typ FOREIGN KEY (typ_łóżka_id) REFERENCES rezerwacje_hotelowe.typ_łóżka (id)
);

CREATE TABLE rezerwacje_hotelowe.status_pokoju (
    id SERIAL PRIMARY KEY,
    nazwa_statusu VARCHAR(100) NOT NULL
);

CREATE TABLE rezerwacje_hotelowe.piętro (
    id SERIAL PRIMARY KEY,
    numer_piętra VARCHAR(5) NOT NULL
);

CREATE TABLE rezerwacje_hotelowe.pokój (
    id SERIAL PRIMARY KEY,
    piętro_id INT NOT NULL,
    klasa_pokoju_id INT NOT NULL,
    status_pokoju_id INT NOT NULL,
    numer_pokoju VARCHAR(10) NOT NULL UNIQUE,
    max_liczba_osob INT,
    CONSTRAINT fk_pokój_piętro FOREIGN KEY (piętro_id) REFERENCES rezerwacje_hotelowe.piętro (id),
    CONSTRAINT fk_pokój_klasa_pokoju FOREIGN KEY (klasa_pokoju_id) REFERENCES rezerwacje_hotelowe.klasa_pokoju (id),
    CONSTRAINT fk_pokój_status_pokoju FOREIGN KEY (status_pokoju_id) REFERENCES rezerwacje_hotelowe.status_pokoju (id)
);

CREATE TABLE rezerwacje_hotelowe.rezerwacja_pokój (
    rezerwacja_id INT NOT NULL,
    pokój_id INT NOT NULL,
    PRIMARY KEY (rezerwacja_id, pokój_id),
    CONSTRAINT fk_rez_pok_rezerwacja FOREIGN KEY (rezerwacja_id) REFERENCES rezerwacje_hotelowe.rezerwacja (id),
    CONSTRAINT fk_rez_pok_pokój FOREIGN KEY (pokój_id) REFERENCES rezerwacje_hotelowe.pokój (id)
);

CREATE TABLE rezerwacje_hotelowe.rola (
    id SERIAL PRIMARY KEY,
    nazwa_roli VARCHAR(50) NOT NULL
);

CREATE TABLE rezerwacje_hotelowe.pracownik (
    id SERIAL PRIMARY KEY,
    imię VARCHAR(200) NOT NULL,
    nazwisko VARCHAR(200) NOT NULL,
    login VARCHAR(100) UNIQUE NOT NULL,
    hasło VARCHAR(256) NOT NULL,
    rola_id INT NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_pracownik_rola FOREIGN KEY (rola_id) REFERENCES rezerwacje_hotelowe.rola (id)
);

CREATE TABLE rezerwacje_hotelowe.logi_systemowe (
    id SERIAL PRIMARY KEY,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    opis VARCHAR(500),
    szczegóły JSONB
);

-- Funkcja obliczająca koszt rezerwacji
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.oblicz_koszt_rezerwacji(p_pokoj_id INT, p_liczba_dni INT, p_rezerwacja_id INT DEFAULT NULL)
RETURNS NUMERIC AS $$
BEGIN
    RETURN (
        SELECT
            (k.cena_podstawowa * p_liczba_dni) + COALESCE(SUM(d.cena), 0)
        FROM
            rezerwacje_hotelowe.pokój p
        JOIN
            rezerwacje_hotelowe.klasa_pokoju k ON p.klasa_pokoju_id = k.id
        LEFT JOIN
            rezerwacje_hotelowe.rezerwacja_dodatek rd ON rd.rezerwacja_id = p_rezerwacja_id
        LEFT JOIN
            rezerwacje_hotelowe.dodatek d ON rd.dodatek_id = d.id
        WHERE
            p.id = p_pokoj_id
        GROUP BY
            k.cena_podstawowa
    );
END;
$$ LANGUAGE plpgsql;

-- Funkcja obliczająca koszt rezerwacji z parametrami
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.oblicz_koszt_rezerwacji_z_parametrami(
    p_room_ids INT[],        -- Tablica ID pokoi
    p_start_date DATE,       -- Data zameldowania
    p_end_date DATE,         -- Data wymeldowania
    p_addons INT[] DEFAULT NULL -- Opcjonalna tablica dodatków
)
RETURNS NUMERIC AS $$
DECLARE
    total_cost NUMERIC := 0; -- Całkowity koszt
    liczba_dni INT;          -- Liczba dni pobytu
    addons_cost NUMERIC := 0; -- Koszt dodatków
BEGIN
    -- Oblicz liczbę dni pobytu
    SELECT (p_end_date - p_start_date) INTO liczba_dni;

    IF liczba_dni <= 0 THEN
        RAISE EXCEPTION 'Nieprawidłowy zakres dat: data wymeldowania musi być późniejsza niż data zameldowania.';
    END IF;

    -- Oblicz koszt pokoi
    SELECT SUM(k.cena_podstawowa * liczba_dni)
    INTO total_cost
    FROM rezerwacje_hotelowe.pokój p
    JOIN rezerwacje_hotelowe.klasa_pokoju k ON p.klasa_pokoju_id = k.id
    WHERE p.id = ANY(p_room_ids);

    -- Oblicz koszt dodatków, jeśli są podane
    IF p_addons IS NOT NULL AND array_length(p_addons, 1) > 0 THEN
        SELECT SUM(d.cena * liczba_dni)
        INTO addons_cost
        FROM rezerwacje_hotelowe.dodatek d
        WHERE d.id = ANY(p_addons);
    END IF;

    -- Dodaj koszt dodatków do całkowitego kosztu
    total_cost := total_cost + addons_cost;

    -- Zwróć całkowity koszt
    RETURN COALESCE(total_cost, 0);
END;
$$ LANGUAGE plpgsql;

-- Funkcja dodająca nową rezerwację
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.dodaj_rezerwacje(
    gosc_id INT,
    pokoje INT[],
    start_date DATE,
    end_date DATE,
    liczba_doroslych INT,
    liczba_dzieci INT,
    dodatki INT[] DEFAULT NULL
)
RETURNS VOID AS $$
DECLARE
    new_rezerwacja_id INT;
    koszt_pokoju NUMERIC;
    pokoj_id INT;
    liczba_dni INT;
BEGIN
    -- Oblicz liczbę dni pobytu
    SELECT (end_date - start_date) INTO liczba_dni;
    IF liczba_dni <= 0 THEN
        RAISE EXCEPTION 'Nieprawidłowy zakres dat: data wymeldowania musi być późniejsza niż data zameldowania.';
    END IF;

    -- Iteracja przez pokoje
    FOREACH pokoj_id IN ARRAY pokoje LOOP
        -- Dodanie nowej rezerwacji
        INSERT INTO rezerwacje_hotelowe.rezerwacja (
            gość_id, status_płatności_id, status_rezerwacji_id, data_zameldowania, data_wymeldowania,
            liczba_dorosłych, liczba_dzieci, kwota_rezerwacji
        )
        VALUES (
            gosc_id,
            (SELECT id FROM rezerwacje_hotelowe.status_płatności WHERE nazwa_statusu = 'Oczekująca'),
            (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Oczekująca'),
            start_date, end_date, liczba_doroslych, liczba_dzieci, 0
        )
        RETURNING id INTO new_rezerwacja_id;

        -- Logowanie dodania rezerwacji
        INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
        VALUES (
            'Dodano nową rezerwację',
            jsonb_build_object(
                'rezerwacja_id', new_rezerwacja_id,
                'gość_id', gosc_id,
                'pokoje', pokoje,
                'data_zameldowania', start_date,
                'data_wymeldowania', end_date,
                'liczba_dorosłych', liczba_doroslych,
                'liczba_dzieci', liczba_dzieci
            )
        );

        -- Obliczanie kosztów
        koszt_pokoju :=rezerwacje_hotelowe.oblicz_koszt_rezerwacji_z_parametrami(pokoje, start_date, end_date, dodatki);

        -- Przydzielenie pokoju do rezerwacji
        INSERT INTO rezerwacje_hotelowe.rezerwacja_pokój (rezerwacja_id, pokój_id)
        VALUES (new_rezerwacja_id, pokoj_id);

        -- Dodanie dodatków
        IF dodatki IS NOT NULL THEN
            INSERT INTO rezerwacje_hotelowe.rezerwacja_dodatek (rezerwacja_id, dodatek_id)
            SELECT new_rezerwacja_id, unnest(dodatki);
            INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
            VALUES (
                'Dodano dodatki do rezerwacji',
                jsonb_build_object(
                    'rezerwacja_id', new_rezerwacja_id,
                    'dodatki', dodatki
                )
            );
        END IF;

        -- Aktualizacja kosztów rezerwacji
        UPDATE rezerwacje_hotelowe.rezerwacja
        SET kwota_rezerwacji = koszt_pokoju
        WHERE id = new_rezerwacja_id;
    END LOOP;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia(
    imie VARCHAR,                    -- Imię nowego gościa
    nazwisko VARCHAR,                -- Nazwisko nowego gościa
    numer_telefonu VARCHAR,          -- Wymagany numer telefonu
    adres_email VARCHAR,             -- Wymagany adres e-mail
    pokoje INT[],                    -- Tablica ID pokoi
    start_date DATE,                 -- Data zameldowania
    end_date DATE,                   -- Data wymeldowania
    liczba_doroslych INT,            -- Liczba dorosłych
    liczba_dzieci INT,               -- Liczba dzieci
    dodatki INT[] DEFAULT NULL       -- Opcjonalna tablica dodatków
)
RETURNS VOID AS $$
DECLARE
    new_gosc_id INT;                 -- ID nowo utworzonego gościa
BEGIN
    -- Walidacja danych wejściowych dla gościa
    IF imie IS NULL OR nazwisko IS NULL OR numer_telefonu IS NULL OR adres_email IS NULL THEN
        RAISE EXCEPTION 'Brak wymaganych danych: imię, nazwisko, numer telefonu lub adres e-mail.';
    END IF;

    -- Tworzenie nowego gościa
    INSERT INTO rezerwacje_hotelowe.gość (imię, nazwisko, numer_telefonu, adres_email)
    VALUES (imie, nazwisko, numer_telefonu, adres_email)
    RETURNING id INTO new_gosc_id;

    -- Wywołanie funkcji dodaj_rezerwacje dla nowo utworzonego gościa
    PERFORM rezerwacje_hotelowe.dodaj_rezerwacje(
        new_gosc_id,
        pokoje,
        start_date,
        end_date,
        liczba_doroslych,
        liczba_dzieci,
        dodatki
    );

    -- Logowanie dodania gościa i rezerwacji
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Dodano nowego gościa i jego rezerwację',
        jsonb_build_object(
            'gosc', jsonb_build_object(
                'imię', imie,
                'nazwisko', nazwisko,
                'numer_telefonu', numer_telefonu,
                'adres_email', adres_email
            ),
            'rezerwacja', jsonb_build_object(
                'pokoje', pokoje,
                'data_zameldowania', start_date,
                'data_wymeldowania', end_date,
                'liczba_doroslych', liczba_doroslych,
                'liczba_dzieci', liczba_dzieci,
                'dodatki', dodatki
            )
        )
    );

    -- Informacja o zakończonej operacji
    RAISE NOTICE 'Gość % został dodany z ID: %, a jego rezerwacja została utworzona.', imie || ' ' || nazwisko, new_gosc_id;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(
    p_imie VARCHAR,                    -- Imię gościa
    p_nazwisko VARCHAR,                -- Nazwisko gościa
    p_numer_telefonu VARCHAR,          -- Numer telefonu
    p_adres_email VARCHAR,             -- Adres e-mail
    pokoje INT[],                    -- Tablica ID pokoi
    start_date DATE,                 -- Data zameldowania
    end_date DATE,                   -- Data wymeldowania
    liczba_doroslych INT,            -- Liczba dorosłych
    liczba_dzieci INT,               -- Liczba dzieci
    dodatki INT[] DEFAULT NULL       -- Opcjonalna tablica dodatków
)
RETURNS VOID AS $$
DECLARE
    existing_gosc RECORD;            -- Rekord istniejącego gościa
    changed_fields JSONB := '{}'::JSONB; -- JSONB do logowania zmian
BEGIN
    -- Walidacja danych wejściowych dla gościa
    IF p_imie IS NULL OR p_nazwisko IS NULL OR p_numer_telefonu IS NULL OR p_adres_email IS NULL THEN
        RAISE EXCEPTION 'Brak wymaganych danych: imię, nazwisko, numer telefonu lub adres e-mail.';
    END IF;

    -- Sprawdzenie, czy gość o danym adresie email już istnieje
    SELECT * INTO existing_gosc FROM rezerwacje_hotelowe.gość WHERE adres_email = p_adres_email;

    IF FOUND THEN
        -- Porównanie i aktualizacja danych gościa, jeśli się zmieniły
        IF existing_gosc.imię <> p_imie THEN
            changed_fields := changed_fields || jsonb_build_object('imię', jsonb_build_object('stare', existing_gosc.imię, 'nowe', p_imie));
            UPDATE rezerwacje_hotelowe.gość SET imię = p_imie WHERE id = existing_gosc.id;
        END IF;

        IF existing_gosc.nazwisko <> p_nazwisko THEN
            changed_fields := changed_fields || jsonb_build_object('nazwisko', jsonb_build_object('stare', existing_gosc.nazwisko, 'nowe', p_nazwisko));
            UPDATE rezerwacje_hotelowe.gość SET nazwisko = p_nazwisko WHERE id = existing_gosc.id;
        END IF;

        IF existing_gosc.numer_telefonu <> p_numer_telefonu THEN
            changed_fields := changed_fields || jsonb_build_object('numer_telefonu', jsonb_build_object('stare', existing_gosc.numer_telefonu, 'nowe', p_numer_telefonu));
            UPDATE rezerwacje_hotelowe.gość SET numer_telefonu = p_numer_telefonu WHERE id = existing_gosc.id;
        END IF;

        -- Logowanie zmian, jeśli takie wystąpiły
        IF changed_fields <> '{}'::JSONB THEN
            INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
            VALUES (
                'Zaktualizowano dane gościa',
                jsonb_build_object(
                    'gość_id', existing_gosc.id,
                    'zmiany', changed_fields
                )
            );
        END IF;

        -- Dodanie rezerwacji dla istniejącego gościa
        PERFORM rezerwacje_hotelowe.dodaj_rezerwacje(
            existing_gosc.id,
            pokoje,
            start_date,
            end_date,
            liczba_doroslych,
            liczba_dzieci,
            dodatki
        );

        -- Logowanie dodania rezerwacji
        INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
        VALUES (
            'Dodano rezerwację dla istniejącego gościa',
            jsonb_build_object(
                'gość_id', existing_gosc.id,
                'rezerwacja', jsonb_build_object(
                    'pokoje', pokoje,
                    'data_zameldowania', start_date,
                    'data_wymeldowania', end_date,
                    'liczba_doroslych', liczba_doroslych,
                    'liczba_dzieci', liczba_dzieci,
                    'dodatki', dodatki
                )
            )
        );

        -- Informacja o zakończonej operacji
        RAISE NOTICE 'Dodano rezerwację dla istniejącego gościa: % %', existing_gosc.imię, existing_gosc.nazwisko;
    ELSE
        -- Wywołanie istniejącej funkcji do dodania nowego gościa i rezerwacji
        PERFORM rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia(
            p_imie,
            p_nazwisko,
            p_numer_telefonu,
            p_adres_email,
            pokoje,
            start_date,
            end_date,
            liczba_doroslych,
            liczba_dzieci,
            dodatki
        );

        -- Informacja o zakończonej operacji
        RAISE NOTICE 'Dodano nowego gościa i jego rezerwację: % %', p_imie, p_nazwisko;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Funkcja aktualizująca rezerwację
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.zaktualizuj_rezerwacje(
    p_reservation_id INT,
    p_guest_id INT,
    p_check_in_date DATE,
    p_check_out_date DATE,
    p_rooms INT[],
    p_payment_status_id INT,
    p_reservation_status_id INT,
    p_amount NUMERIC,
    p_addons INT[] DEFAULT NULL
)
RETURNS VOID AS $$
DECLARE
    new_total_cost NUMERIC;
BEGIN
    -- Sprawdzenie, czy istnieje rezerwacja o podanym ID
    IF NOT EXISTS (
        SELECT 1
        FROM rezerwacje_hotelowe.rezerwacja
        WHERE id = p_reservation_id
    ) THEN
        RAISE EXCEPTION 'Rezerwacja o ID % nie istnieje.', p_reservation_id;
    END IF;

    -- Sprawdzenie, czy istnieje gość o podanym ID
    IF NOT EXISTS (
        SELECT 1
        FROM rezerwacje_hotelowe.gość
        WHERE id = p_guest_id
    ) THEN
        RAISE EXCEPTION 'Gość o ID % nie istnieje.', p_guest_id;
    END IF;

    -- Sprawdzenie, czy istnieje status rezerwacji o podanym ID
    IF NOT EXISTS (
        SELECT 1
        FROM rezerwacje_hotelowe.status_rezerwacji
        WHERE id = p_reservation_status_id
    ) THEN
        RAISE EXCEPTION 'Status rezerwacji o ID % nie istnieje.', p_reservation_status_id;
    END IF;

    -- Sprawdzenie dostępności pokoi w podanym zakresie dat, ignorując obecną rezerwację
    IF EXISTS (
        SELECT 1
        FROM rezerwacje_hotelowe.rezerwacja_pokój rp
        JOIN rezerwacje_hotelowe.rezerwacja r ON rp.rezerwacja_id = r.id
        WHERE rp.pokój_id = ANY(p_rooms)
          AND r.id != p_reservation_id
          AND r.data_zameldowania < p_check_out_date
          AND r.data_wymeldowania > p_check_in_date
    ) THEN
        RAISE EXCEPTION 'Jeden lub więcej pokoi jest zajętych w podanym zakresie dat.';
    END IF;

    -- Aktualizacja szczegółów rezerwacji
    UPDATE rezerwacje_hotelowe.rezerwacja
    SET
        gość_id = p_guest_id,
        data_zameldowania = p_check_in_date,
        data_wymeldowania = p_check_out_date,
        status_płatności_id = p_payment_status_id,
        status_rezerwacji_id = p_reservation_status_id,
        kwota_rezerwacji = p_amount
    WHERE id = p_reservation_id;

    -- Aktualizacja przypisanych pokoi
    DELETE FROM rezerwacje_hotelowe.rezerwacja_pokój WHERE rezerwacja_id = p_reservation_id;
    INSERT INTO rezerwacje_hotelowe.rezerwacja_pokój (rezerwacja_id, pokój_id)
    SELECT p_reservation_id, unnest(p_rooms);

    -- Aktualizacja dodatków
    DELETE FROM rezerwacje_hotelowe.rezerwacja_dodatek WHERE rezerwacja_id = p_reservation_id;
    IF p_addons IS NOT NULL AND array_length(p_addons, 1) > 0 THEN
        INSERT INTO rezerwacje_hotelowe.rezerwacja_dodatek (rezerwacja_id, dodatek_id)
        SELECT p_reservation_id, unnest(p_addons);
    END IF;

    -- Obliczenie nowego całkowitego kosztu
    new_total_cost := rezerwacje_hotelowe.oblicz_koszt_rezerwacji_z_parametrami(
        p_rooms,
        p_check_in_date,
        p_check_out_date,
        p_addons
    );

    -- Aktualizacja kosztu rezerwacji
    UPDATE rezerwacje_hotelowe.rezerwacja
    SET kwota_rezerwacji = new_total_cost
    WHERE id = p_reservation_id;

    -- Logowanie zmiany
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Zaktualizowano rezerwację',
        jsonb_build_object(
            'rezerwacja_id', p_reservation_id,
            'nowy_gość_id', p_guest_id,
            'pokoje', p_rooms,
            'data_zameldowania', p_check_in_date,
            'data_wymeldowania', p_check_out_date,
            'status_płatności_id', p_payment_status_id,
            'status_rezerwacji_id', p_reservation_status_id,
            'nowa_kwota', new_total_cost,
            'dodatki', p_addons
        )
    );

    -- Informacja o zakończonej operacji
    RAISE NOTICE 'Rezerwacja o ID % została zaktualizowana.', p_reservation_id;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.pobierz_szczegoly_rezerwacji(p_reservation_id INT)
RETURNS JSON AS $$
DECLARE
    reservation_json JSON;
BEGIN
    -- Pobierz główne szczegóły rezerwacji wraz z dodatkami, pokojem i statusem rezerwacji
    SELECT json_build_object(
        'id', r.id,
        'guest_id', r."gość_id",
        'guest_name', g."imię" || ' ' || g."nazwisko",
        'check_in', r."data_zameldowania",
        'check_out', r."data_wymeldowania",
        'payment_status_id', r."status_płatności_id",
        'payment_status', s."nazwa_statusu",
        'reservation_status_id', r."status_rezerwacji_id",
        'reservation_status', sr."nazwa_statusu",
        'amount', r."kwota_rezerwacji",
        'room', (
            SELECT json_build_object(
                'room_id', rp."pokój_id",
                'room_number', p."numer_pokoju"
            )
            FROM rezerwacje_hotelowe.rezerwacja_pokój rp
            JOIN rezerwacje_hotelowe.pokój p ON rp."pokój_id" = p.id
            WHERE rp."rezerwacja_id" = r.id
            LIMIT 1 -- Zakładamy, że jest tylko jeden pokój na rezerwację
        ),
        'selected_addons', (
            SELECT json_agg(json_build_object(
                'id', d.id,
                'nazwa_dodatku', d."nazwa_dodatku",
                'cena', d.cena
            ))
            FROM rezerwacje_hotelowe.rezerwacja_dodatek rd
            JOIN rezerwacje_hotelowe.dodatek d ON rd."dodatek_id" = d.id
            WHERE rd."rezerwacja_id" = r.id
        )
    ) INTO reservation_json
    FROM rezerwacje_hotelowe.rezerwacja r
    JOIN rezerwacje_hotelowe.gość g ON r."gość_id" = g.id
    JOIN rezerwacje_hotelowe.status_płatności s ON r."status_płatności_id" = s.id
    JOIN rezerwacje_hotelowe.status_rezerwacji sr ON r."status_rezerwacji_id" = sr.id
    WHERE r.id = p_reservation_id;

    -- Sprawdź, czy rezerwacja istnieje
    IF reservation_json IS NULL THEN
        RETURN json_build_object('success', false, 'error', 'Rezerwacja o podanym ID nie istnieje');
    ELSE
        RETURN json_build_object('success', true, 'reservation', reservation_json);
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Funkcja usuwająca rezerwację
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.usun_rezerwacje(p_rezerwacja_id INT)
RETURNS VOID AS $$
DECLARE
    data_zameldowania DATE;
    dzisiejsza_data DATE := CURRENT_DATE;
    roznica_dni INT;
BEGIN
    -- Pobierz datę zameldowania dla danej rezerwacji
    SELECT r.data_zameldowania
    INTO data_zameldowania
    FROM rezerwacje_hotelowe.rezerwacja r
    WHERE r.id = p_rezerwacja_id;

    -- Sprawdź, czy rezerwacja istnieje
    IF NOT FOUND THEN
        RAISE EXCEPTION 'Rezerwacja o podanym ID nie istnieje.';
    END IF;

    -- Oblicz różnicę dni między dzisiejszą datą a datą zameldowania
    roznica_dni := data_zameldowania - dzisiejsza_data;

    -- Walidacja: usuń tylko, jeśli różnica dni >= 31
    IF roznica_dni < 31 THEN
        RAISE EXCEPTION 'Nie można usunąć rezerwacji, gdy do zameldowania pozostało mniej niż 31 dni.';
    END IF;

    -- Usuń powiązania w tabeli rezerwacja_pokój
    DELETE FROM rezerwacje_hotelowe.rezerwacja_pokój
    WHERE rezerwacja_id = p_rezerwacja_id;

    -- Usuń powiązania w tabeli rezerwacja_dodatek
    DELETE FROM rezerwacje_hotelowe.rezerwacja_dodatek
    WHERE rezerwacja_id = p_rezerwacja_id;

    -- Usuń rezerwację
    DELETE FROM rezerwacje_hotelowe.rezerwacja
    WHERE id = p_rezerwacja_id;

    -- Logowanie usunięcia rezerwacji
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Usunięto rezerwację',
        jsonb_build_object(
            'rezerwacja_id', p_rezerwacja_id,
            'data_usuniecia', CURRENT_TIMESTAMP
        )
    );

    -- Komunikat potwierdzający
    RAISE NOTICE 'Rezerwacja o ID % została pomyślnie usunięta.', p_rezerwacja_id;
END;
$$ LANGUAGE plpgsql;

-- Funkcja anulująca rezerwację
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.anuluj_rezerwacje(p_rezerwacja_id INT)
RETURNS VOID AS $$
BEGIN
    -- Aktualizacja statusu rezerwacji na "Anulowana"
    UPDATE rezerwacje_hotelowe.rezerwacja
    SET status_rezerwacji_id = (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Anulowana')
    WHERE id = p_rezerwacja_id;

    -- Zwolnienie pokoi związanych z rezerwacją
    UPDATE rezerwacje_hotelowe.pokój
    SET status_pokoju_id = (SELECT id FROM rezerwacje_hotelowe.status_pokoju WHERE nazwa_statusu = 'Dostępny')
    WHERE id IN (
        SELECT pokój_id FROM rezerwacje_hotelowe.rezerwacja_pokój WHERE rezerwacja_id = p_rezerwacja_id
    );

    -- Logowanie anulowania rezerwacji
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Anulowano rezerwację',
        jsonb_build_object(
            'rezerwacja_id', p_rezerwacja_id,
            'data_anulowania', CURRENT_TIMESTAMP
        )
    );

    -- Informacja o zakończonej operacji
    RAISE NOTICE 'Rezerwacja o ID % została anulowana.', p_rezerwacja_id;
END;
$$ LANGUAGE plpgsql;

-- Funkcja zmieniająca status pokoju
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.zmien_status_pokoju(pokoj_id INT, status VARCHAR)
RETURNS VOID AS $$
BEGIN
    UPDATE rezerwacje_hotelowe.pokój
    SET status_pokoju_id = (SELECT id FROM rezerwacje_hotelowe.status_pokoju WHERE nazwa_statusu = status)
    WHERE id = pokoj_id;

    -- Logowanie zmiany statusu pokoju
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Zmieniono status pokoju',
        jsonb_build_object(
            'pokój_id', pokoj_id,
            'nowy_status', status,
            'data_zmiany', CURRENT_TIMESTAMP
        )
    );
END;
$$ LANGUAGE plpgsql;

-- Funkcja wykwaterowująca rezerwację
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.wykwateruj_rezerwacje(p_rezerwacja_id INT)
RETURNS VOID AS $$
DECLARE
    pokoj_id INT;
    rezerwacja_rec rezerwacje_hotelowe.rezerwacja%ROWTYPE;
BEGIN
    -- Sprawdź, czy istnieje rezerwacja o podanym ID
    IF NOT EXISTS (
        SELECT 1
        FROM rezerwacje_hotelowe.rezerwacja
        WHERE id = p_rezerwacja_id
    ) THEN
        RAISE EXCEPTION 'Rezerwacja o ID % nie istnieje.', p_rezerwacja_id;
    END IF;

    -- Pobierz rekord rezerwacji
    SELECT *
    INTO rezerwacja_rec
    FROM rezerwacje_hotelowe.rezerwacja
    WHERE id = p_rezerwacja_id;

    -- Sprawdź, czy status płatności jest "Zrealizowana"
    IF (SELECT nazwa_statusu FROM rezerwacje_hotelowe.status_płatności WHERE id = rezerwacja_rec.status_płatności_id) != 'Zrealizowana' THEN
        RAISE EXCEPTION 'Rezerwacja musi mieć status płatności "Zrealizowana", aby można ją było wykwaterować.';
    END IF;

    -- Pobierz ID pokoju związanego z rezerwacją
    SELECT pokój_id INTO pokoj_id
    FROM rezerwacje_hotelowe.rezerwacja_pokój
    WHERE rezerwacja_id = p_rezerwacja_id;

    -- Zmień status pokoju na „Dostępny”
    PERFORM rezerwacje_hotelowe.zmien_status_pokoju(pokoj_id, 'Dostępny');

    -- Aktualizuj status rezerwacji na „Zrealizowana”
    UPDATE rezerwacje_hotelowe.rezerwacja
    SET status_rezerwacji_id = (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Zrealizowana')
    WHERE id = p_rezerwacja_id;

    -- Logowanie wykwaterowania
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Wykwaterowano rezerwację',
        jsonb_build_object(
            'rezerwacja_id', p_rezerwacja_id,
            'pokój_id', pokoj_id,
            'data_wykwaterowania', CURRENT_DATE
        )
    );

    -- Informacja o zakończonej operacji
    RAISE NOTICE 'Rezerwacja o ID % została wykwaterowana i oznaczona jako „Zrealizowana”. Pokój % jest teraz dostępny.', p_rezerwacja_id, pokoj_id;
END;
$$ LANGUAGE plpgsql;

-- Funkcja sprawdzająca dostępność pokojów w określonym terminie (głównie do debugowania)
CREATE OR REPLACE FUNCTION sprawdz_dostepne_pokoje(start_date DATE, end_date DATE)
RETURNS TABLE(pokoj_id INT, numer_pokoju VARCHAR) AS $$
BEGIN
    RETURN QUERY
    SELECT
        p.id,
        p.numer_pokoju
    FROM
        rezerwacje_hotelowe.pokój p
    LEFT JOIN
        rezerwacje_hotelowe.rezerwacja_pokój rp ON p.id = rp.pokój_id
    LEFT JOIN
        rezerwacje_hotelowe.rezerwacja r ON rp.rezerwacja_id = r.id
    WHERE
        p.status_pokoju_id = (SELECT id FROM rezerwacje_hotelowe.status_pokoju WHERE nazwa_statusu = 'Dostępny')
        AND (
            r.data_zameldowania IS NULL
            OR r.data_wymeldowania < start_date
            OR r.data_zameldowania > end_date
        )
    ORDER BY p.numer_pokoju;
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Sprawdzono dostępne pokoje',
        jsonb_build_object(
            'data_od', start_date,
            'data_do', end_date
        )
    );
END;
$$ LANGUAGE plpgsql;

-- Tworzenie funkcji sprawdzającej dostępność pokojów z parametrami
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.sprawdz_dostepne_pokoje_z_parametrami(
    p_start_date DATE DEFAULT NULL,
    p_end_date DATE DEFAULT NULL,
    p_liczba_osob INT DEFAULT NULL,
    p_klasa_pokoju_id INT DEFAULT NULL
)
RETURNS TABLE (
    pokoj_id INT,
    numer_pokoju VARCHAR,
    nazwa_klasy VARCHAR,
    max_liczba_osob INT
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        p.id AS pokoj_id,
        p.numer_pokoju,
        k.nazwa_klasy,
        p.max_liczba_osob
    FROM
        rezerwacje_hotelowe.pokój p
    JOIN
        rezerwacje_hotelowe.klasa_pokoju k ON p.klasa_pokoju_id = k.id
    LEFT JOIN
        rezerwacje_hotelowe.rezerwacja_pokój rp ON p.id = rp.pokój_id
    LEFT JOIN
        rezerwacje_hotelowe.rezerwacja r ON rp.rezerwacja_id = r.id
    WHERE
        -- Filtr liczby osób
        (p_liczba_osob IS NULL OR p.max_liczba_osob >= p_liczba_osob)

        -- Filtr klasy pokoju
        AND (p_klasa_pokoju_id IS NULL OR p.klasa_pokoju_id = p_klasa_pokoju_id)

        -- Logika alternatywy:
        AND (
            -- Opcja 1: Pokój jest dostępny (status "Dostępny")
            p.status_pokoju_id = (SELECT id FROM rezerwacje_hotelowe.status_pokoju WHERE nazwa_statusu = 'Dostępny')

            -- Opcja 2: Pokój będzie dostępny w podanym terminie
            OR (
                p_start_date IS NOT NULL AND p_end_date IS NOT NULL
                AND NOT EXISTS (
                    SELECT 1
                    FROM rezerwacje_hotelowe.rezerwacja r2
                    JOIN rezerwacje_hotelowe.rezerwacja_pokój rp2 ON r2.id = rp2.rezerwacja_id
                    WHERE rp2.pokój_id = p.id
                    AND (
                        -- Kolizja terminów z uwzględnieniem dnia wymeldowania
                        r2.data_zameldowania < p_end_date AND r2.data_wymeldowania > p_start_date
                    )
                )
            )
        )
    ORDER BY
        p.numer_pokoju;
END;
$$ LANGUAGE plpgsql;

-- Funkcja aktualizująca status płatności
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.aktualizuj_status_platnosci(
    p_rezerwacja_id INT,
    p_nowy_status VARCHAR
)
RETURNS VOID AS $$
DECLARE
    p_status_id INT;
BEGIN
    -- Pobierz ID nowego statusu płatności
    SELECT id INTO p_status_id
    FROM rezerwacje_hotelowe.status_płatności
    WHERE nazwa_statusu = p_nowy_status;

    IF p_status_id IS NULL THEN
        RAISE EXCEPTION 'Nie znaleziono statusu płatności: %', p_nowy_status;
    END IF;

    -- Aktualizuj status płatności w rezerwacji
    UPDATE rezerwacje_hotelowe.rezerwacja
    SET status_płatności_id = p_status_id
    WHERE id = p_rezerwacja_id;

    -- Logowanie zmiany statusu
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Zmieniono status płatności',
        jsonb_build_object(
            'rezerwacja_id', p_rezerwacja_id,
            'nowy_status', p_nowy_status
        )
    );

    RAISE NOTICE 'Zaktualizowano status płatności dla rezerwacji % na %.', p_rezerwacja_id, p_nowy_status;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION trig_zmien_status_platnosci()
RETURNS TRIGGER AS $$
BEGIN
    -- Wywołaj funkcję aktualizującą status płatności
    PERFORM rezerwacje_hotelowe.aktualizuj_status_platnosci(NEW.id, (SELECT nazwa_statusu FROM rezerwacje_hotelowe.status_płatności WHERE id = NEW.status_płatności_id));

    -- Zwróć nowy rekord, aby kontynuować proces aktualizacji
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trig_aktualizuj_status_platnosci
AFTER UPDATE OF status_rezerwacji_id ON rezerwacje_hotelowe.rezerwacja
FOR EACH ROW
WHEN (OLD.status_rezerwacji_id IS DISTINCT FROM NEW.status_rezerwacji_id)
EXECUTE FUNCTION trig_zmien_status_platnosci();

-- Funkcja aktualizująca datę płatności na podstawie statusu
CREATE OR REPLACE FUNCTION aktualizuj_date_platnosci()
RETURNS TRIGGER AS $$
BEGIN
    -- Sprawdzenie, czy status zmienia się na "Zrealizowana"
    IF NEW.nazwa_statusu = 'Zrealizowana' THEN
        NEW.data_płatności := CURRENT_TIMESTAMP; -- Ustaw aktualną datę i godzinę
    END IF;
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
        VALUES (
            'Zmieniono status płatności na Zrealizowana',
            jsonb_build_object(
                'id_statusu', NEW.id,
                'nowa_data_płatności', NEW.data_płatności,
                'poprzedni_status', OLD.nazwa_statusu
            )
        );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trig_aktualizuj_date_platnosci
BEFORE UPDATE ON rezerwacje_hotelowe.status_płatności
FOR EACH ROW
EXECUTE FUNCTION aktualizuj_date_platnosci();

-- Funkcja wyliczająca maksymalną liczbę osób w pokoju na podstawie typu łóżek
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.aktualizuj_max_liczba_osob()
RETURNS TRIGGER AS $$
BEGIN
    SELECT COALESCE(SUM(tlp.liczba_łóżek * t.liczba_osob), 0)
      INTO NEW.max_liczba_osob
      FROM rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy AS tlp
      JOIN rezerwacje_hotelowe.typ_łóżka AS t ON tlp.typ_łóżka_id = t.id
     WHERE tlp.klasa_pokoju_id = NEW.klasa_pokoju_id;

    IF NEW.max_liczba_osob IS NULL THEN
        NEW.max_liczba_osob := 0;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger do obliczania maksymalnej liczby osób w pokoju przy INSERT lub UPDATE
CREATE OR REPLACE TRIGGER trig_aktualizuj_max_liczba_osob
BEFORE INSERT OR UPDATE ON rezerwacje_hotelowe.pokój
FOR EACH ROW
EXECUTE FUNCTION rezerwacje_hotelowe.aktualizuj_max_liczba_osob();

CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.aktualizuj_max_liczba_osob_po_zmianie_lozek()
RETURNS TRIGGER AS $$
BEGIN
    -- Aktualizujemy max_liczba_osób dla każdego pokoju w zmienionej klasie
    UPDATE rezerwacje_hotelowe.pokój
    SET max_liczba_osob = (
        SELECT COALESCE(SUM(tlp.liczba_łóżek * t.liczba_osob), 0)
        FROM rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy tlp
        JOIN rezerwacje_hotelowe.typ_łóżka t ON tlp.typ_łóżka_id = t.id
        WHERE tlp.klasa_pokoju_id = pokój.klasa_pokoju_id
    )
    WHERE klasa_pokoju_id = (
        CASE
            WHEN TG_OP = 'DELETE' THEN OLD.klasa_pokoju_id
            ELSE NEW.klasa_pokoju_id
        END
    );

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trig_aktualizuj_max_liczba_osob_po_zmianie_lozek
AFTER INSERT OR UPDATE OR DELETE ON rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy
FOR EACH ROW
EXECUTE FUNCTION rezerwacje_hotelowe.aktualizuj_max_liczba_osob_po_zmianie_lozek();

-- Tworzenie funkcji zmieniającej status pokoju na "Zajęty" po przypisaniu rezerwacji
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.aktualizuj_status_po_rezerwacji()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM rezerwacje_hotelowe.zmien_status_pokoju(NEW.pokój_id, 'Zajęty');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trig_aktualizuj_status_po_rezerwacji
AFTER INSERT ON rezerwacje_hotelowe.rezerwacja_pokój
FOR EACH ROW
EXECUTE FUNCTION rezerwacje_hotelowe.aktualizuj_status_po_rezerwacji();

CREATE OR REPLACE FUNCTION aktualizuj_status_po_wymeldowaniu()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM rezerwacje_hotelowe.zmien_status_pokoju(OLD.pokój_id, 'Dostępny');
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trig_aktualizuj_status_po_wymeldowaniu
AFTER DELETE ON rezerwacje_hotelowe.rezerwacja_pokój
FOR EACH ROW
EXECUTE FUNCTION aktualizuj_status_po_wymeldowaniu();

-- Funkcja wykwaterowująca rezerwację
CREATE OR REPLACE FUNCTION rezerwacje_hotelowe.wykwateruj_rezerwacje(p_rezerwacja_id INT)
RETURNS VOID AS $$
DECLARE
    pokoj_id INT;
    rezerwacja_rec rezerwacje_hotelowe.rezerwacja%ROWTYPE;
BEGIN
    -- Sprawdź, czy istnieje rezerwacja o podanym ID
    IF NOT EXISTS (
        SELECT 1
        FROM rezerwacje_hotelowe.rezerwacja
        WHERE id = p_rezerwacja_id
    ) THEN
        RAISE EXCEPTION 'Rezerwacja o ID % nie istnieje.', p_rezerwacja_id;
    END IF;

    -- Pobierz rekord rezerwacji
    SELECT *
    INTO rezerwacja_rec
    FROM rezerwacje_hotelowe.rezerwacja
    WHERE id = p_rezerwacja_id;

    -- Sprawdź, czy status płatności jest "Zrealizowana"
    IF (SELECT nazwa_statusu FROM rezerwacje_hotelowe.status_płatności WHERE id = rezerwacja_rec.status_płatności_id) != 'Zrealizowana' THEN
        RAISE EXCEPTION 'Rezerwacja musi mieć status płatności "Zrealizowana", aby można ją było wykwaterować.';
    END IF;

    -- Pobierz ID pokoju związanego z rezerwacją
    SELECT pokój_id INTO pokoj_id
    FROM rezerwacje_hotelowe.rezerwacja_pokój
    WHERE rezerwacja_id = p_rezerwacja_id;

    -- Zmień status pokoju na „Dostępny”
    PERFORM rezerwacje_hotelowe.zmien_status_pokoju(pokoj_id, 'Dostępny');

    -- Aktualizuj status rezerwacji na „Zrealizowana”
    UPDATE rezerwacje_hotelowe.rezerwacja
    SET status_rezerwacji_id = (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Zrealizowana')
    WHERE id = p_rezerwacja_id;

    -- Logowanie wykwaterowania
    INSERT INTO rezerwacje_hotelowe.logi_systemowe (opis, szczegóły)
    VALUES (
        'Wykwaterowano rezerwację',
        jsonb_build_object(
            'rezerwacja_id', p_rezerwacja_id,
            'pokój_id', pokoj_id,
            'data_wykwaterowania', CURRENT_DATE
        )
    );

    -- Informacja o zakończonej operacji
    RAISE NOTICE 'Rezerwacja o ID % została wykwaterowana i oznaczona jako „Zrealizowana”. Pokój % jest teraz dostępny.', p_rezerwacja_id, pokoj_id;
END;
$$ LANGUAGE plpgsql;

-- Dodawanie ról
INSERT INTO rezerwacje_hotelowe.rola (nazwa_roli) VALUES ('Administrator'), ('Recepcjonista'), ('Manager');

-- Dodawanie statusów płatności
INSERT INTO rezerwacje_hotelowe.status_płatności (nazwa_statusu) VALUES ('Oczekująca'), ('Zrealizowana'), ('Anulowana');

-- Dodawanie statusów pokoju
INSERT INTO rezerwacje_hotelowe.status_pokoju (nazwa_statusu) VALUES ('Dostępny'), ('Zajęty'), ('W trakcie sprzątania');

-- Dodawanie wyposażenia
INSERT INTO rezerwacje_hotelowe.wyposażenie (nazwa_wyposażenia) VALUES
('Telewizor'),
('Klimatyzacja'),
('Wi-Fi'),
('Minibar');

-- Dodawanie typów łóżek
INSERT INTO rezerwacje_hotelowe.typ_łóżka (nazwa_typu, liczba_osob) VALUES
('Pojedyncze', 1),
('Podwójne', 2),
('Królewskie', 2),
('Kanapa', 1);

-- Dodawanie klas pokoi z wariantami
INSERT INTO rezerwacje_hotelowe.klasa_pokoju (nazwa_klasy, cena_podstawowa) VALUES
('Standard', 200.00),
('Standard z kanapą', 250.00),
('Standard z 3 łóżkami pojedynczymi', 270.00),
('Deluxe', 350.00),
('Deluxe z 2 łóżkami podwójnymi', 400.00),
('Deluxe z łóżkiem królewskim i pojedynczym', 380.00),
('Apartament', 500.00),
('Apartament 5-osobowy', 600.00),
('Apartament 8-osobowy', 800.00);

-- Dodawanie wyposażenia dla pokoi każdej klasy
INSERT INTO rezerwacje_hotelowe.wyposażenie_pokoju_danej_klasy (klasa_pokoju_id, wyposażenie_id) VALUES
(1, 1), -- Standard z Telewizorem
(1, 3), -- Standard z Wi-Fi
(2, 1), -- Standard z kanapą i Telewizorem
(2, 3), -- Standard z kanapą i Wi-Fi
(3, 1), -- Standard z 3 łóżkami pojedynczymi i Telewizorem
(3, 3), -- Standard z 3 łóżkami pojedynczymi i Wi-Fi
(4, 1), -- Deluxe z Telewizorem
(4, 2), -- Deluxe z Klimatyzacją
(4, 3), -- Deluxe z Wi-Fi
(5, 1), -- Deluxe z 2 łóżkami podwójnymi i Telewizorem
(5, 2), -- Deluxe z 2 łóżkami podwójnymi i Klimatyzacją
(5, 3), -- Deluxe z 2 łóżkami podwójnymi i Wi-Fi
(6, 1), -- Deluxe z łóżkiem królewskim i pojedynczym
(6, 2),
(6, 3), -- Deluxe z Wi-Fi
(7, 1), -- Apartament z Telewizorem
(7, 2), -- Apartament z Klimatyzacją
(7, 3), -- Apartament z Wi-Fi
(7, 4), -- Apartament z Minibarem
(8, 1),
(8, 2), -- Apartament 5-osobowy z Telewizorem
(8, 3), -- Apartament 5-osobowy z Wi-Fi
(8, 4),
(9, 1), -- Apartament 8-osobowy z Telewizorem
(9, 2), -- Apartament 8-osobowy z Klimatyzacją
(9, 3), -- Apartament 8-osobowy z Wi-Fi
(9, 4); -- Apartament 8-osobowy z Minibarem

-- Dodawanie typów łóżek do klas pokoi
INSERT INTO rezerwacje_hotelowe.typ_łóżka_pokoju_danej_klasy (klasa_pokoju_id, typ_łóżka_id, liczba_łóżek) VALUES
-- Standard
(1, 1, 2), -- Standard z 2 łóżkami pojedynczymi
(2, 4, 1), -- Standard z 1 kanapą
(2, 1, 2),
(3, 1, 3), -- Standard z 3 łóżkami pojedynczymi
-- Deluxe
(4, 2, 1), -- Deluxe z 1 łóżkiem podwójnym
(5, 2, 2), -- Deluxe z 2 łóżkami podwójnymi
(6, 3, 1), -- Deluxe z 1 łóżkiem królewskim
(6, 1, 1), -- Deluxe z 1 łóżkiem pojedynczym
-- Apartament
(7, 3, 1), -- Apartament z 1 łóżkiem królewskim
(8, 1, 5), -- Apartament 5-osobowy z 5 łóżkami pojedynczymi
(9, 1, 8); -- Apartament 8-osobowy z 8 łóżkami pojedynczymi

-- Dodawanie pięter
INSERT INTO rezerwacje_hotelowe.piętro (numer_piętra) VALUES ('1'), ('2'), ('3');

-- Dodawanie pokoi
INSERT INTO rezerwacje_hotelowe.pokój (piętro_id, klasa_pokoju_id, status_pokoju_id, numer_pokoju) VALUES
-- Standard
(1, 1, 1, '101'),
(1, 2, 1, '102'), -- Standard z kanapą
(1, 3, 1, '103'), -- Standard z 3 łóżkami pojedynczymi
-- Deluxe
(2, 4, 1, '201'), -- Deluxe
(2, 5, 1, '202'), -- Deluxe z 2 łóżkami podwójnymi
(2, 6, 1, '203'), -- Deluxe z łóżkiem królewskim i pojedynczym
-- Apartament
(3, 7, 1, '301'), -- Apartament
(3, 8, 1, '302'), -- Apartament 5-osobowy
(3, 9, 1, '303'); -- Apartament 8-osobowy

-- Dodawanie dodatków
INSERT INTO rezerwacje_hotelowe.dodatek (nazwa_dodatku, cena) VALUES ('Śniadanie', 30.00), ('Parking', 20.00), ('Spa', 100.00);

-- Dodawanie gości
INSERT INTO rezerwacje_hotelowe.gość (imię, nazwisko, numer_telefonu, adres_email) VALUES
('Adam', 'Nowak', '123456789', 'adam.nowak@example.com'),
('Ewa', 'Kowalska', '987654321', 'ewa.kowalska@example.com');

-- Dodawanie rezerwacji
INSERT INTO rezerwacje_hotelowe.rezerwacja (gość_id, status_płatności_id, status_rezerwacji_id, data_zameldowania, data_wymeldowania, liczba_dorosłych, liczba_dzieci, kwota_rezerwacji)
VALUES
(1, 1, (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Oczekująca'), '2023-12-01', '2023-12-05', 2, 0, 800.00),
(2, 2, (SELECT id FROM rezerwacje_hotelowe.status_rezerwacji WHERE nazwa_statusu = 'Potwierdzona'), '2023-12-10', '2023-12-15', 2, 1, 1500.00);

-- Przypisywanie pokoi do rezerwacji
INSERT INTO rezerwacje_hotelowe.rezerwacja_pokój (rezerwacja_id, pokój_id) VALUES
(1, 1),
(2, 3);

-- Przypisywanie dodatków do rezerwacji
INSERT INTO rezerwacje_hotelowe.rezerwacja_dodatek (rezerwacja_id, dodatek_id) VALUES
(1, 1), -- Śniadanie do rezerwacji 1
(1, 2), -- Parking do rezerwacji 1
(2, 1), -- Śniadanie do rezerwacji 2
(2, 3); -- Spa do rezerwacji 2

-- Dodawanie pracowników
INSERT INTO rezerwacje_hotelowe.pracownik (imię, nazwisko, login, hasło, rola_id)
VALUES
('Dawid', 'Piotrowski', 'dawid.piotrowski', md5('admin123'),
 (SELECT id FROM rezerwacje_hotelowe.rola WHERE nazwa_roli = 'Administrator')),
('Ewa', 'Kowalska', 'ewa.kowalska', md5('recep123'),
 (SELECT id FROM rezerwacje_hotelowe.rola WHERE nazwa_roli = 'Recepcjonista')),
('Jan', 'Nowak', 'jan.nowak', md5('manager123'),
 (SELECT id FROM rezerwacje_hotelowe.rola WHERE nazwa_roli = 'Manager')),
('Anna', 'Wiśniewska', 'anna.wisniewska', md5('recep456'),
 (SELECT id FROM rezerwacje_hotelowe.rola WHERE nazwa_roli = 'Recepcjonista'));

-- Tworzenie widoków
CREATE OR REPLACE VIEW rezerwacje_hotelowe.liczba_rezerwacji_gościa AS
SELECT
    g.id AS gość_id,
    g.imię,
    g.nazwisko,
    COUNT(r.id) FILTER (WHERE sr.nazwa_statusu NOT IN ('Anulowana')) AS liczba_aktywnych_rezerwacji,
    COUNT(r.id) AS liczba_wszystkich_rezerwacji
FROM
    rezerwacje_hotelowe.gość g
LEFT JOIN
    rezerwacje_hotelowe.rezerwacja r ON g.id = r.gość_id
LEFT JOIN
    rezerwacje_hotelowe.status_rezerwacji sr ON r.status_rezerwacji_id = sr.id
GROUP BY
    g.id, g.imię, g.nazwisko;

CREATE OR REPLACE VIEW rezerwacje_hotelowe.przychód_miesięczny AS
SELECT
    TO_CHAR(DATE_TRUNC('month', r.data_zameldowania), 'YYYY-MM') AS miesiąc,
    SUM(r.kwota_rezerwacji) AS suma_przychodów
FROM rezerwacje_hotelowe.rezerwacja r
JOIN rezerwacje_hotelowe.status_rezerwacji sr ON r.status_rezerwacji_id = sr.id
WHERE sr.nazwa_statusu = 'Zrealizowana'
GROUP BY DATE_TRUNC('month', r.data_zameldowania)
ORDER BY DATE_TRUNC('month', r.data_zameldowania);


CREATE OR REPLACE VIEW rezerwacje_hotelowe.przychód_miesięczny_filtr AS
SELECT
    TO_CHAR(DATE_TRUNC('month', r.data_zameldowania), 'YYYY-MM') AS miesiąc,
    SUM(r.kwota_rezerwacji) AS suma_przychodów
FROM rezerwacje_hotelowe.rezerwacja r
JOIN rezerwacje_hotelowe.status_rezerwacji sr ON r.status_rezerwacji_id = sr.id
WHERE sr.nazwa_statusu = 'Zrealizowana'
GROUP BY miesiąc
HAVING SUM(r.kwota_rezerwacji) > 3000
ORDER BY miesiąc;

-- Przykładowe wywołania widoków
SELECT *
FROM rezerwacje_hotelowe.liczba_rezerwacji_gościa;

SELECT *
FROM rezerwacje_hotelowe.przychód_miesięczny;

SELECT *
FROM rezerwacje_hotelowe.przychód_miesięczny_filtr;

-- Przykładowe zapytania testowe
SELECT * FROM rezerwacje_hotelowe.sprawdz_dostepne_pokoje_z_parametrami(NULL, NULL, NULL, NULL);
SELECT * FROM rezerwacje_hotelowe.sprawdz_dostepne_pokoje_z_parametrami('2024-12-10', '2024-12-13', 2, 4);


SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia(
    'Marcin',                               -- imię
    'Grabowski',                        -- nazwisko
    '999123456',                   -- numer telefonu (9 cyfr OK)
    'marcin.grab@example.com',        -- adres e-mail
    ARRAY[3],                             -- pokoje: np. pokój_id = 3
    '2024-03-10',                      -- data_zameldowania
    '2024-03-14',                       -- data_wymeldowania
    2,                            -- liczba dorosłych
    1,                               -- liczba dzieci
    ARRAY[1,3]                           -- dodatki: np. id=1 (Śniadanie), id=3 (Spa)
);

-- Przykładowe wywołania funkcji dla celów statystycznych
SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(
    'Kasia',
    'Zielińska',
    '+44987654321',        -- inny prefix np. +44
    'kasia.z@example.com',
    ARRAY[4],
    '2025-01-02',
    '2025-01-05',
    1,
    1,
    ARRAY[3]
);

-- Przykład, w którym aktualizujemy nowe nazwisko gościa
SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(
    'Kasia',
    'Zielińska-Korczak',
    '+44987654321',        -- inny prefix np. +44
    'kasia.z@example.com',
    ARRAY[4],
    '2025-05-02',
    '2025-05-05',
    1,
    1,
    ARRAY[3]
);

SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(
    'Adam',                             -- to samo imię co w bazie
    'Nowak',                        -- to samo nazwisko co w bazie
    '+48123456789',            -- CHCEMY zaktualizować jego numer
    'adam.nowak@example.com',
    ARRAY[2,3],                         -- rezerwuje dwa pokoje na raz
    '2024-07-01',
    '2024-07-05',
    2,
    2,
    ARRAY[1,2,3]                       -- trzy różne dodatki
);

-- Próba z błędnym numerem telefonu
-- SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia(
--     'Zbigniew',
--     'Niedziela',
--     '1234567',          -- tylko 7 cyfr, constraint powinien wyrzucić błąd
--     'z.niedziela@example.com',
--     ARRAY[1],
--     '2024-05-01',
--     '2024-05-05',
--     2,
--     0
-- );


-- Próba z błędnym numerem telefonu
-- SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia(
--     'Zbigniew',
--     'Niedziela',
--     '1234567',          -- tylko 7 cyfr, constraint powinien wyrzucić błąd
--     'z.niedziela@example.com',
--     ARRAY[1],
--     '2024-05-01',
--     '2024-05-05',
--     2,
--     0
-- );

-- Próba z e-mailem, który nie występuje w bazie
SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(
    'Kasia',
    'Zielińska',
    '+44987654321',        -- inny prefix
    'kasia.z@example.com',
    ARRAY[4],
    '2025-02-02',
    '2025-02-05',
    1,
    1,
    ARRAY[3]
);
-- Przypominam, że tutaj nazwisko znów powróci do poprzedniego stanu a informacja zostanie zapisana w logach


-- Przykład z niepoprawną datą
-- SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_nowego_goscia(
--     'Helena',
--     'Sowa',
--     '+48988999111',
--     'helena.sowa@example.com',
--     ARRAY[2],
--     '2024-06-10',  -- start
--     '2024-06-09',  -- koniec dzień wcześniej => błąd
--     2,
--     2,
--     ARRAY[2]
-- );

-- Błędny numer telefonu (11 cyfr, brak plusa)
-- SELECT rezerwacje_hotelowe.dodaj_rezerwacje_przez_goscia_public(
--     'Jacek',
--     'Rozmus',
--     '12345678901', -- 11 cyfr, a nie 9
--     'jacek.r@example.com',
--     ARRAY[3],
--     '2025-02-10',
--     '2025-02-12',
--     2,
--     0
-- );

-- Dodawanie rezerwacji dla istniejącego gościa
SELECT rezerwacje_hotelowe.dodaj_rezerwacje(
    2,                      -- gość_id
    ARRAY[5],                -- pokój_id=5
    '2024-05-15',
    '2024-05-20',
    2,
    1,
    ARRAY[2]                -- dodatek = 2 (Parking)
);

-- Modyfikacja rezerwacji
SELECT rezerwacje_hotelowe.zaktualizuj_rezerwacje(
    1,                      -- rezerwacja_id
    1,                          -- gość_id = 1 (Adam Nowak)
    '2023-12-02',
    '2023-12-06',
    ARRAY[2],                     -- zmieniamy pokój, np. na pokój_id=2
    2,                   -- status_płatności_id
    2,                 -- status_rezerwacji_id (np. 'Potwierdzona')
    1300.00,                     -- tymczasowa kwota
    ARRAY[1,2]                   -- dodatki: Śniadanie + Parking
);

-- Przykład anulowania rezerwacji
SELECT rezerwacje_hotelowe.anuluj_rezerwacje(3);

SELECT rezerwacje_hotelowe.wykwateruj_rezerwacje(2);

-- Próba wykwaterowania, gdy status płatności jest inny niż Zrealizowana
-- SELECT rezerwacje_hotelowe.wykwateruj_rezerwacje(1);

-- Zmiana statusu pokoju (poza powyższymi, których nie omówiłem, mamy wersję demo logów, które zapisują niektóre zmiany)
SELECT rezerwacje_hotelowe.zmien_status_pokoju(1, 'W trakcie sprzątania');
