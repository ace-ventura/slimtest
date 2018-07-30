--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4
-- Dumped by pg_dump version 10.4

-- Started on 2018-07-30 07:11:50

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 1 (class 3079 OID 12924)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2823 (class 0 OID 0)
-- Dependencies: 1
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 200 (class 1259 OID 16424)
-- Name: recipe; Type: TABLE; Schema: public; Owner: simple_user
--

CREATE TABLE public.recipe (
    title character(50) NOT NULL,
    text text,
    composition jsonb,
    recipe_id integer NOT NULL,
    user_id bigint,
    picture character(50)
);


ALTER TABLE public.recipe OWNER TO simple_user;

--
-- TOC entry 199 (class 1259 OID 16422)
-- Name: recipe_recipe_id_seq; Type: SEQUENCE; Schema: public; Owner: simple_user
--

CREATE SEQUENCE public.recipe_recipe_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.recipe_recipe_id_seq OWNER TO simple_user;

--
-- TOC entry 2824 (class 0 OID 0)
-- Dependencies: 199
-- Name: recipe_recipe_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: simple_user
--

ALTER SEQUENCE public.recipe_recipe_id_seq OWNED BY public.recipe.recipe_id;


--
-- TOC entry 198 (class 1259 OID 16416)
-- Name: session; Type: TABLE; Schema: public; Owner: simple_user
--

CREATE TABLE public.session (
    user_id integer,
    session_id character(50) NOT NULL
);


ALTER TABLE public.session OWNER TO simple_user;

--
-- TOC entry 196 (class 1259 OID 16395)
-- Name: user; Type: TABLE; Schema: public; Owner: simple_user
--

CREATE TABLE public."user" (
    full_name character(20),
    login character(20),
    pwd character(100),
    user_id bigint NOT NULL
);


ALTER TABLE public."user" OWNER TO simple_user;

--
-- TOC entry 197 (class 1259 OID 16398)
-- Name: user_user_id_seq; Type: SEQUENCE; Schema: public; Owner: simple_user
--

CREATE SEQUENCE public.user_user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_user_id_seq OWNER TO simple_user;

--
-- TOC entry 2825 (class 0 OID 0)
-- Dependencies: 197
-- Name: user_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: simple_user
--

ALTER SEQUENCE public.user_user_id_seq OWNED BY public."user".user_id;


--
-- TOC entry 2683 (class 2604 OID 16427)
-- Name: recipe recipe_id; Type: DEFAULT; Schema: public; Owner: simple_user
--

ALTER TABLE ONLY public.recipe ALTER COLUMN recipe_id SET DEFAULT nextval('public.recipe_recipe_id_seq'::regclass);


--
-- TOC entry 2682 (class 2604 OID 24578)
-- Name: user user_id; Type: DEFAULT; Schema: public; Owner: simple_user
--

ALTER TABLE ONLY public."user" ALTER COLUMN user_id SET DEFAULT nextval('public.user_user_id_seq'::regclass);


--
-- TOC entry 2815 (class 0 OID 16424)
-- Dependencies: 200
-- Data for Name: recipe; Type: TABLE DATA; Schema: public; Owner: simple_user
--

COPY public.recipe (title, text, composition, recipe_id, user_id, picture) FROM stdin;
\.


--
-- TOC entry 2813 (class 0 OID 16416)
-- Dependencies: 198
-- Data for Name: session; Type: TABLE DATA; Schema: public; Owner: simple_user
--

COPY public.session (user_id, session_id) FROM stdin;
\.


--
-- TOC entry 2811 (class 0 OID 16395)
-- Dependencies: 196
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: simple_user
--

COPY public."user" (full_name, login, pwd, user_id) FROM stdin;
F                   	admin               	827ccb0eea8a706c4c34a16891f84e7b                                                                    	1
\.


--
-- TOC entry 2826 (class 0 OID 0)
-- Dependencies: 199
-- Name: recipe_recipe_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simple_user
--

SELECT pg_catalog.setval('public.recipe_recipe_id_seq', 6, true);


--
-- TOC entry 2827 (class 0 OID 0)
-- Dependencies: 197
-- Name: user_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: simple_user
--

SELECT pg_catalog.setval('public.user_user_id_seq', 16, true);


--
-- TOC entry 2689 (class 2606 OID 16432)
-- Name: recipe recipe_pkey; Type: CONSTRAINT; Schema: public; Owner: simple_user
--

ALTER TABLE ONLY public.recipe
    ADD CONSTRAINT recipe_pkey PRIMARY KEY (recipe_id);


--
-- TOC entry 2687 (class 2606 OID 24607)
-- Name: session session_pkey; Type: CONSTRAINT; Schema: public; Owner: simple_user
--

ALTER TABLE ONLY public.session
    ADD CONSTRAINT session_pkey PRIMARY KEY (session_id);


--
-- TOC entry 2685 (class 2606 OID 24583)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: simple_user
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (user_id);


-- Completed on 2018-07-30 07:11:52

--
-- PostgreSQL database dump complete
--

