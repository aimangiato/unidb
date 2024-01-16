--
-- PostgreSQL database dump
--

-- Dumped from database version 15.5
-- Dumped by pg_dump version 15.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: unidb; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA unidb;


ALTER SCHEMA unidb OWNER TO postgres;

--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: aggiorna_numero_docente(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.aggiorna_numero_docente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE unidb.progressivo SET numero_docente = numero_docente + 1;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.aggiorna_numero_docente() OWNER TO postgres;

--
-- Name: aggiorna_numero_studente(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.aggiorna_numero_studente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE unidb.progressivo SET numero_studente = numero_studente + 1;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.aggiorna_numero_studente() OWNER TO postgres;

--
-- Name: check_inserimento_esame(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.check_inserimento_esame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

begin 

perform *
FROM unidb.insegnamento i INNER JOIN unidb.esame e ON i.codice_i = e.codice_i
WHERE e.data_esame = new.data_esame AND e.codice_cdl = new.codice_cdl AND i.anno_erogazione = (
	select anno_erogazione
	from unidb.insegnamento
	where insegnamento.codice_i = new.codice_i AND insegnamento.codice_cdl = new.codice_cdl
);

IF FOUND THEN RAISE 'Errore: in questa data è già previsto un esame dello stesso CDL e anno di erogazione'; RETURN NULL;
ELSE RETURN NEW;
END IF;

END;
$$;


ALTER FUNCTION public.check_inserimento_esame() OWNER TO postgres;

--
-- Name: check_iscrizione_esame(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.check_iscrizione_esame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$

DECLARE 
propedeuticita_richieste INT;
propedeuticita_passate INT;

BEGIN

SELECT COUNT(propedeuticita) INTO propedeuticita_richieste
FROM unidb.propedeuticita
WHERE codice_i = (
	select e.codice_i
	from unidb.insegnamento i inner join unidb.esame e on i.codice_i = e.codice_i
	where new.codice_esame = e.codice_esame
);

SELECT COUNT(distinct i.codice_i) INTO propedeuticita_passate
FROM unidb.carriera_esame c inner join unidb.esame e on c.codice_esame = e.codice_esame 
INNER JOIN unidb.insegnamento i ON e.codice_i = i.codice_i
WHERE c.matricola = new.matricola AND c.codice_i in (
	select propedeuticita
	from unidb.propedeuticita
	where codice_i = (
		select e1.codice_i
		from unidb.insegnamento i1 inner join unidb.esame e1 on i1.codice_i = e1.codice_i
		where new.codice_esame = e1.codice_esame
	)
)AND c.voto >= 18;

IF propedeuticita_richieste = propedeuticita_passate THEN RETURN NEW;
ELSE RAISE 'Non puoi iscriverti a questo esame in quanto non fa parte del tuo piano di studi o non hai le propedeuticita richieste.';
RETURN NULL;
END IF;
END;
$$;


ALTER FUNCTION public.check_iscrizione_esame() OWNER TO postgres;

--
-- Name: check_iscrizioni_before_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.check_iscrizioni_before_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  esame_count INTEGER;
BEGIN
  -- Controlla se ci sono iscrizioni per l'esame
  SELECT COUNT(*)
  INTO esame_count
  FROM unidb.iscrizione_esame
  WHERE codice_esame = OLD.codice_esame;

  -- Se ci sono iscrizioni, annulla la cancellazione
  IF esame_count > 0 THEN
    RAISE EXCEPTION 'Impossibile cancellare l''esame, ci sono studenti iscritti.';
  END IF;

  -- Se non ci sono iscrizioni, permetti la cancellazione
  RETURN OLD;
END;
$$;


ALTER FUNCTION public.check_iscrizioni_before_delete() OWNER TO postgres;

--
-- Name: disiscrivi_dopo_verbalizzazione(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.disiscrivi_dopo_verbalizzazione() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  -- Cancella l'iscrizione dello studente all'esame dopo la verbalizzazione
  DELETE FROM unidb.iscrizione_esame
  WHERE matricola = NEW.matricola
    AND codice_esame = NEW.codice_esame;

  RETURN NEW;
END;
$$;


ALTER FUNCTION public.disiscrivi_dopo_verbalizzazione() OWNER TO postgres;

--
-- Name: maxcheck_insegnamenti_docente(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.maxcheck_insegnamenti_docente() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
numero_insegnamenti INT;
BEGIN

SELECT COUNT(codice_i) INTO numero_insegnamenti
FROM unidb.insegnamento i INNER JOIN unidb.docente d on i.docente = d.codice_docente
WHERE new.docente = d.codice_docente;

IF numero_insegnamenti >= 3 THEN RAISE 'Ogni docente può avere al massimo 3 insegnamenti di cui è responsabile.';
RETURN NULL;
ELSE RETURN new;
END IF;

END;
$$;


ALTER FUNCTION public.maxcheck_insegnamenti_docente() OWNER TO postgres;

--
-- Name: studente_carriera_storico(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.studente_carriera_storico() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin 
insert into unidb.storico_carriera
select * from unidb.carriera_esame where matricola = old.matricola;
insert into unidb.storico_studente values(old.matricola, old.nome, old.cognome, old.anno, old.email, old.codice_cdl);
return old;
end;

$$;


ALTER FUNCTION public.studente_carriera_storico() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: carriera_esame; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.carriera_esame (
    matricola character varying(10) NOT NULL,
    codice_esame character varying(10) NOT NULL,
    voto numeric,
    codice_i character varying(10),
    CONSTRAINT carriera_esame_voto_check CHECK (((voto > (0)::numeric) AND (voto < (32)::numeric)))
);


ALTER TABLE unidb.carriera_esame OWNER TO postgres;

--
-- Name: cdl; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.cdl (
    codice_cdl character varying(50) NOT NULL,
    nome character varying(30),
    tipo character varying(20),
    descrizione text,
    CONSTRAINT cdl_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['triennale'::character varying, 'magistrale'::character varying, 'ciclo unico'::character varying])::text[])))
);


ALTER TABLE unidb.cdl OWNER TO postgres;

--
-- Name: docente; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.docente (
    codice_docente character varying(10) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL,
    email character varying(50) NOT NULL
);


ALTER TABLE unidb.docente OWNER TO postgres;

--
-- Name: esame; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.esame (
    codice_esame character varying(10) NOT NULL,
    codice_i character varying(10) NOT NULL,
    codice_cdl character varying(10) NOT NULL,
    data_esame date,
    luogo character varying(50)
);


ALTER TABLE unidb.esame OWNER TO postgres;

--
-- Name: insegnamento; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.insegnamento (
    codice_i character varying(10) NOT NULL,
    codice_cdl character varying(10) NOT NULL,
    docente character varying(10) NOT NULL,
    nome character varying(30),
    descrizione text,
    anno_erogazione integer,
    CONSTRAINT anno_erogazione CHECK (((anno_erogazione > 0) AND (anno_erogazione < 4)))
);


ALTER TABLE unidb.insegnamento OWNER TO postgres;

--
-- Name: iscrizione_esame; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.iscrizione_esame (
    matricola character varying(10) NOT NULL,
    codice_esame character varying(10) NOT NULL
);


ALTER TABLE unidb.iscrizione_esame OWNER TO postgres;

--
-- Name: progressivo; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.progressivo (
    id integer NOT NULL,
    numero_studente integer DEFAULT 0,
    numero_docente integer DEFAULT 0
);


ALTER TABLE unidb.progressivo OWNER TO postgres;

--
-- Name: progressivo_id_seq; Type: SEQUENCE; Schema: unidb; Owner: postgres
--

CREATE SEQUENCE unidb.progressivo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE unidb.progressivo_id_seq OWNER TO postgres;

--
-- Name: progressivo_id_seq; Type: SEQUENCE OWNED BY; Schema: unidb; Owner: postgres
--

ALTER SEQUENCE unidb.progressivo_id_seq OWNED BY unidb.progressivo.id;


--
-- Name: propedeuticita; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.propedeuticita (
    codice_i character varying(10) NOT NULL,
    propedeuticita character varying(10) NOT NULL
);


ALTER TABLE unidb.propedeuticita OWNER TO postgres;

--
-- Name: segreteria; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.segreteria (
    email character varying(50) NOT NULL
);


ALTER TABLE unidb.segreteria OWNER TO postgres;

--
-- Name: storico_carriera; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.storico_carriera (
    matricola character varying(10) NOT NULL,
    codice_esame character varying(10) NOT NULL,
    voto numeric,
    codice_i character varying(10),
    CONSTRAINT storico_carriera_voto_check CHECK (((voto > (0)::numeric) AND (voto < (31)::numeric)))
);


ALTER TABLE unidb.storico_carriera OWNER TO postgres;

--
-- Name: storico_studente; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.storico_studente (
    matricola character varying(10) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL,
    anno integer,
    email character varying(50),
    codice_cdl character varying(10),
    CONSTRAINT storico_studente_anno_check CHECK (((anno > 0) AND (anno < 4)))
);


ALTER TABLE unidb.storico_studente OWNER TO postgres;

--
-- Name: studente; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.studente (
    matricola character varying(10) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL,
    anno integer,
    email character varying(50) NOT NULL,
    codice_cdl character varying(10) NOT NULL,
    CONSTRAINT studente_anno_check CHECK (((anno > 0) AND (anno < 4)))
);


ALTER TABLE unidb.studente OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: unidb; Owner: postgres
--

CREATE TABLE unidb.users (
    email character varying(50) NOT NULL,
    password character varying(50) NOT NULL,
    utente character varying(50) NOT NULL
);


ALTER TABLE unidb.users OWNER TO postgres;

--
-- Name: progressivo id; Type: DEFAULT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.progressivo ALTER COLUMN id SET DEFAULT nextval('unidb.progressivo_id_seq'::regclass);


--
-- Name: carriera_esame carriera_esame_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.carriera_esame
    ADD CONSTRAINT carriera_esame_pkey PRIMARY KEY (matricola, codice_esame);


--
-- Name: cdl cdl_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.cdl
    ADD CONSTRAINT cdl_pkey PRIMARY KEY (codice_cdl);


--
-- Name: propedeuticita check_diversi; Type: CHECK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE unidb.propedeuticita
    ADD CONSTRAINT check_diversi CHECK (((codice_i)::text <> (propedeuticita)::text)) NOT VALID;


--
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (codice_docente);


--
-- Name: esame esame_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.esame
    ADD CONSTRAINT esame_pkey PRIMARY KEY (codice_esame);


--
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (codice_i);


--
-- Name: iscrizione_esame iscrizione_esame_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.iscrizione_esame
    ADD CONSTRAINT iscrizione_esame_pkey PRIMARY KEY (matricola, codice_esame);


--
-- Name: progressivo progressivo_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.progressivo
    ADD CONSTRAINT progressivo_pkey PRIMARY KEY (id);


--
-- Name: propedeuticita propedeuticita_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.propedeuticita
    ADD CONSTRAINT propedeuticita_pkey PRIMARY KEY (codice_i, propedeuticita);


--
-- Name: segreteria segreteria_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.segreteria
    ADD CONSTRAINT segreteria_pkey PRIMARY KEY (email);


--
-- Name: storico_carriera storico_carriera_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.storico_carriera
    ADD CONSTRAINT storico_carriera_pkey PRIMARY KEY (matricola, codice_esame);


--
-- Name: storico_studente storico_studente_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.storico_studente
    ADD CONSTRAINT storico_studente_pkey PRIMARY KEY (matricola);


--
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (matricola);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (email);


--
-- Name: carriera_esame after_insert_carriera_esame; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER after_insert_carriera_esame AFTER INSERT ON unidb.carriera_esame FOR EACH ROW EXECUTE FUNCTION public.disiscrivi_dopo_verbalizzazione();


--
-- Name: esame before_delete_esame; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER before_delete_esame BEFORE DELETE ON unidb.esame FOR EACH ROW EXECUTE FUNCTION public.check_iscrizioni_before_delete();


--
-- Name: iscrizione_esame iscrizione_trigger; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER iscrizione_trigger BEFORE INSERT ON unidb.iscrizione_esame FOR EACH ROW EXECUTE FUNCTION public.check_iscrizione_esame();


--
-- Name: insegnamento max_insegnamenti_trigger; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER max_insegnamenti_trigger BEFORE INSERT OR UPDATE ON unidb.insegnamento FOR EACH ROW EXECUTE FUNCTION public.maxcheck_insegnamenti_docente();


--
-- Name: esame programmazione_esame_trigger; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER programmazione_esame_trigger BEFORE INSERT OR UPDATE ON unidb.esame FOR EACH ROW EXECUTE FUNCTION public.check_inserimento_esame();


--
-- Name: docente progressivo_docenti; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER progressivo_docenti BEFORE INSERT ON unidb.docente FOR EACH ROW EXECUTE FUNCTION public.aggiorna_numero_docente();


--
-- Name: studente progressivo_studenti; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER progressivo_studenti BEFORE INSERT ON unidb.studente FOR EACH ROW EXECUTE FUNCTION public.aggiorna_numero_studente();


--
-- Name: studente storico_trigger; Type: TRIGGER; Schema: unidb; Owner: postgres
--

CREATE TRIGGER storico_trigger BEFORE DELETE ON unidb.studente FOR EACH ROW EXECUTE FUNCTION public.studente_carriera_storico();


--
-- Name: carriera_esame carriera_esame_codice_esame_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.carriera_esame
    ADD CONSTRAINT carriera_esame_codice_esame_fkey FOREIGN KEY (codice_esame) REFERENCES unidb.esame(codice_esame) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: carriera_esame carriera_esame_matricola_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.carriera_esame
    ADD CONSTRAINT carriera_esame_matricola_fkey FOREIGN KEY (matricola) REFERENCES unidb.studente(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: docente docente_email_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.docente
    ADD CONSTRAINT docente_email_fkey FOREIGN KEY (email) REFERENCES unidb.users(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: esame esame_codice_cdl_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.esame
    ADD CONSTRAINT esame_codice_cdl_fkey FOREIGN KEY (codice_cdl) REFERENCES unidb.cdl(codice_cdl) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: esame esame_codice_i_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.esame
    ADD CONSTRAINT esame_codice_i_fkey FOREIGN KEY (codice_i) REFERENCES unidb.insegnamento(codice_i) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: carriera_esame fk_insegnamento; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.carriera_esame
    ADD CONSTRAINT fk_insegnamento FOREIGN KEY (codice_i) REFERENCES unidb.insegnamento(codice_i) ON UPDATE CASCADE;


--
-- Name: insegnamento insegnamento_codice_cdl_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.insegnamento
    ADD CONSTRAINT insegnamento_codice_cdl_fkey FOREIGN KEY (codice_cdl) REFERENCES unidb.cdl(codice_cdl) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: insegnamento insegnamento_docente_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.insegnamento
    ADD CONSTRAINT insegnamento_docente_fkey FOREIGN KEY (docente) REFERENCES unidb.docente(codice_docente) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: storico_carriera insegnamento_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.storico_carriera
    ADD CONSTRAINT insegnamento_fkey FOREIGN KEY (codice_i) REFERENCES unidb.insegnamento(codice_i) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: iscrizione_esame iscrizione_esame_codice_esame_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.iscrizione_esame
    ADD CONSTRAINT iscrizione_esame_codice_esame_fkey FOREIGN KEY (codice_esame) REFERENCES unidb.esame(codice_esame) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: iscrizione_esame iscrizione_esame_matricola_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.iscrizione_esame
    ADD CONSTRAINT iscrizione_esame_matricola_fkey FOREIGN KEY (matricola) REFERENCES unidb.studente(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: propedeuticita propedeuticita_codice_i_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.propedeuticita
    ADD CONSTRAINT propedeuticita_codice_i_fkey FOREIGN KEY (codice_i) REFERENCES unidb.insegnamento(codice_i) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: propedeuticita propedeuticita_propedeuticita_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.propedeuticita
    ADD CONSTRAINT propedeuticita_propedeuticita_fkey FOREIGN KEY (propedeuticita) REFERENCES unidb.insegnamento(codice_i) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: segreteria segreteria_email_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.segreteria
    ADD CONSTRAINT segreteria_email_fkey FOREIGN KEY (email) REFERENCES unidb.users(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: storico_carriera storico_carriera_codice_esame_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.storico_carriera
    ADD CONSTRAINT storico_carriera_codice_esame_fkey FOREIGN KEY (codice_esame) REFERENCES unidb.esame(codice_esame) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: storico_carriera storico_carriera_matricola_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.storico_carriera
    ADD CONSTRAINT storico_carriera_matricola_fkey FOREIGN KEY (matricola) REFERENCES unidb.studente(matricola) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: studente studente_codice_cdl_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.studente
    ADD CONSTRAINT studente_codice_cdl_fkey FOREIGN KEY (codice_cdl) REFERENCES unidb.cdl(codice_cdl) ON UPDATE CASCADE;


--
-- Name: studente studente_email_fkey; Type: FK CONSTRAINT; Schema: unidb; Owner: postgres
--

ALTER TABLE ONLY unidb.studente
    ADD CONSTRAINT studente_email_fkey FOREIGN KEY (email) REFERENCES unidb.users(email) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

