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
-- Data for Name: cdl; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.cdl (codice_cdl, nome, tipo, descrizione) FROM stdin;
L31	Informatica	triennale	gli obiettivi del corso sono...
L15	Informatica Musicale	triennale	L'obbiettivo di questo corso di laurea è...
M13	Mediazione linguistica	triennale	L'obiettivo del corso è apprendere le lingue per mediare 
LM77	Management sanitario	magistrale	L'obbietivo di questo corso è di formare future figure manageriali nel settore sanitario
F19	Giurisprudenza	triennale	L'obbiettivo di questo corso di laurea è 
Z1	Biologia	triennale	L'obiettivo del corso è diventare biologi
Z2	Biotecnologie	magistrale	L'obiettivo del corso è diventare biotecnologi
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.users (email, password, utente) FROM stdin;
segre.teria@segreteria.uni	827ccb0eea8a706c4c34a16891f84e7b	segreteria
max.kappa@studenti.uni	827ccb0eea8a706c4c34a16891f84e7b	studente
luca.verdi@studenti.uni	827ccb0eea8a706c4c34a16891f84e7b	studente
leandro.adinolfi@studenti.uni	827ccb0eea8a706c4c34a16891f84e7b	studente
lorenzo.bonarrigo@studenti.uni	827ccb0eea8a706c4c34a16891f84e7b	studente
nicola.basilico@docente.uni	827ccb0eea8a706c4c34a16891f84e7b	docente
giovanni.pighizzini@docente.uni	827ccb0eea8a706c4c34a16891f84e7b	docente
massimo.santini@docente.uni	827ccb0eea8a706c4c34a16891f84e7b	docente
aiman.qaouji@studenti.uni	827ccb0eea8a706c4c34a16891f84e7b	studente
mario.rossi@docente.uni	827ccb0eea8a706c4c34a16891f84e7b	docente
cellone.cellini@docente.uni	827ccb0eea8a706c4c34a16891f84e7b	docente
cito.plasma@studenti.uni	827ccb0eea8a706c4c34a16891f84e7b	studente
vincenzo.piuri@docente.uni	d1a15127005df8e5ebbf3b92ec0e7445	docente
giulia.santis@studenti.uni	faddd43e992655ce8990a59b55f32195	studente
\.


--
-- Data for Name: docente; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.docente (codice_docente, nome, cognome, email) FROM stdin;
D1	Nicola	Basilico	nicola.basilico@docente.uni
D4	Mario	Rossi	mario.rossi@docente.uni
D5	Giovanni	Pighizzini	giovanni.pighizzini@docente.uni
D6	Massimo	Santini	massimo.santini@docente.uni
D7	Cellone	Cellini	cellone.cellini@docente.uni
D8	Vincenzo	Piuri	vincenzo.piuri@docente.uni
\.


--
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.insegnamento (codice_i, codice_cdl, docente, nome, descrizione, anno_erogazione) FROM stdin;
I01	L31	D1	Architettura degli Elaboratori	Archi1	1
I02	L31	D1	Archi2	Archi2	1
I3	L31	D5	Algoritmi e Strutture Dati	Grafi e Alberi	2
I4	L31	D4	Programmazione	golang	1
I5	L31	D6	Prog2	Java	2
I6	Z1	D7	Cellulologia	Scienza delle Cellule	3
\.


--
-- Data for Name: esame; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.esame (codice_esame, codice_i, codice_cdl, data_esame, luogo) FROM stdin;
E2	I02	L31	2024-02-12	Aula 305
E1	I02	L31	2024-03-15	Lambda
E3	I01	L31	2024-01-20	Aula Magna
E4	I4	L31	2024-01-30	Theta
E5	I6	Z1	2024-03-09	Settore Didattico 103
\.


--
-- Data for Name: studente; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.studente (matricola, nome, cognome, anno, email, codice_cdl) FROM stdin;
S2	Aiman	Qaouji	1	aiman.qaouji@studenti.uni	L31
S4	Max	Kappa	1	max.kappa@studenti.uni	L31
S6	Luca	Verdi	1	luca.verdi@studenti.uni	L31
S7	Leandro	Adinolfi	1	leandro.adinolfi@studenti.uni	LM77
S8	Lorenzo	Bonarrigo	1	lorenzo.bonarrigo@studenti.uni	Z2
S9	Cito	Plasma	1	cito.plasma@studenti.uni	Z1
S10	Giulia	Santis	1	giulia.santis@studenti.uni	F19
\.


--
-- Data for Name: carriera_esame; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.carriera_esame (matricola, codice_esame, voto, codice_i) FROM stdin;
S2	E2	10	I02
S2	E3	22	I01
S4	E4	31	I4
\.


--
-- Data for Name: iscrizione_esame; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.iscrizione_esame (matricola, codice_esame) FROM stdin;
S2	E4
S4	E4
S4	E3
S9	E5
\.


--
-- Data for Name: progressivo; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.progressivo (id, numero_studente, numero_docente) FROM stdin;
1	10	8
\.


--
-- Data for Name: propedeuticita; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.propedeuticita (codice_i, propedeuticita) FROM stdin;
I02	I01
I3	I4
\.


--
-- Data for Name: segreteria; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.segreteria (email) FROM stdin;
segre.teria@segreteria.uni
\.


--
-- Data for Name: storico_carriera; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.storico_carriera (matricola, codice_esame, voto, codice_i) FROM stdin;
\.


--
-- Data for Name: storico_studente; Type: TABLE DATA; Schema: unidb; Owner: postgres
--

COPY unidb.storico_studente (matricola, nome, cognome, anno, email, codice_cdl) FROM stdin;
S5	Chiara	Brambati	1	chiara.brambati@studenti.uni	L31
\.


--
-- Name: progressivo_id_seq; Type: SEQUENCE SET; Schema: unidb; Owner: postgres
--

SELECT pg_catalog.setval('unidb.progressivo_id_seq', 1, false);


--
-- PostgreSQL database dump complete
--

