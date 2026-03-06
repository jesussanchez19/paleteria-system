--
-- PostgreSQL database dump
--

\restrict 5OTjA4eWtRHYFb4mQUyz360bkpnFK55m7ROqoaxE5u5dG1M1gfjAkXNMHhcco8V

-- Dumped from database version 16.13 (Debian 16.13-1.pgdg13+1)
-- Dumped by pg_dump version 17.8 (Debian 17.8-0+deb13u1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_logs (
    id bigint NOT NULL,
    user_id bigint,
    action character varying(255) NOT NULL,
    module character varying(255) NOT NULL,
    entity_type character varying(255),
    entity_id bigint,
    meta json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.audit_logs OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.audit_logs_id_seq OWNER TO postgres;

--
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.audit_logs_id_seq OWNED BY public.audit_logs.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: cash_registers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cash_registers (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    opening_amount numeric(10,2) NOT NULL,
    expected_amount numeric(10,2),
    closing_amount numeric(10,2),
    difference numeric(10,2),
    opened_at timestamp(0) without time zone NOT NULL,
    closed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cash_registers OWNER TO postgres;

--
-- Name: cash_registers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cash_registers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cash_registers_id_seq OWNER TO postgres;

--
-- Name: cash_registers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cash_registers_id_seq OWNED BY public.cash_registers.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    price numeric(10,2) NOT NULL,
    stock integer DEFAULT 0 NOT NULL,
    category character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    image character varying(255),
    description text
);


ALTER TABLE public.products OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_seq OWNER TO postgres;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: sale_details; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sale_details (
    id bigint NOT NULL,
    sale_id bigint NOT NULL,
    product_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    qty integer DEFAULT 1 NOT NULL,
    price_unit numeric(10,2) DEFAULT '0'::numeric NOT NULL
);


ALTER TABLE public.sale_details OWNER TO postgres;

--
-- Name: sale_details_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sale_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sale_details_id_seq OWNER TO postgres;

--
-- Name: sale_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sale_details_id_seq OWNED BY public.sale_details.id;


--
-- Name: sales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sales (
    id bigint NOT NULL,
    total numeric(10,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    user_id bigint,
    sold_at timestamp(0) without time zone
);


ALTER TABLE public.sales OWNER TO postgres;

--
-- Name: sales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sales_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sales_id_seq OWNER TO postgres;

--
-- Name: sales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sales_id_seq OWNED BY public.sales.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.settings (
    id bigint NOT NULL,
    key character varying(255) NOT NULL,
    value text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.settings OWNER TO postgres;

--
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.settings_id_seq OWNER TO postgres;

--
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role character varying(255) DEFAULT 'vendedor'::character varying NOT NULL,
    is_active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: weather_snapshots; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.weather_snapshots (
    id bigint NOT NULL,
    date date NOT NULL,
    city character varying(255) NOT NULL,
    temp numeric(5,2),
    condition character varying(255),
    raw json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.weather_snapshots OWNER TO postgres;

--
-- Name: weather_snapshots_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.weather_snapshots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.weather_snapshots_id_seq OWNER TO postgres;

--
-- Name: weather_snapshots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.weather_snapshots_id_seq OWNED BY public.weather_snapshots.id;


--
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs ALTER COLUMN id SET DEFAULT nextval('public.audit_logs_id_seq'::regclass);


--
-- Name: cash_registers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cash_registers ALTER COLUMN id SET DEFAULT nextval('public.cash_registers_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: sale_details id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_details ALTER COLUMN id SET DEFAULT nextval('public.sale_details_id_seq'::regclass);


--
-- Name: sales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales ALTER COLUMN id SET DEFAULT nextval('public.sales_id_seq'::regclass);


--
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: weather_snapshots id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.weather_snapshots ALTER COLUMN id SET DEFAULT nextval('public.weather_snapshots_id_seq'::regclass);


--
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit_logs (id, user_id, action, module, entity_type, entity_id, meta, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: cash_registers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cash_registers (id, user_id, opening_amount, expected_amount, closing_amount, difference, opened_at, closed_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_01_26_055908_create_products_table	1
5	2026_01_26_060338_create_sales_table	1
6	2026_01_26_060400_create_sale_details_table	1
7	2026_01_27_081240_add_fields_to_products_table	1
8	2026_01_27_082700_add_category_and_is_active_to_products_table	1
9	2026_01_27_111447_add_role_to_users_table	1
10	2026_01_27_190852_add_is_active_to_users_table	1
11	2026_01_29_010000_add_user_id_to_sales_table	1
12	2026_01_29_010100_add_sold_at_to_sales_table	1
13	2026_01_29_010200_add_qty_to_sale_details_table	1
14	2026_01_29_010300_add_price_unit_to_sale_details_table	1
15	2026_01_29_010400_drop_quantity_from_sale_details_table	1
16	2026_01_29_010500_drop_price_from_sale_details_table	1
17	2026_03_04_192731_create_cash_registers_table	1
18	2026_03_04_202149_create_audit_logs_table	1
19	2026_03_04_212917_create_settings_table	1
20	2026_03_04_225112_create_weather_snapshots_table	1
21	2026_03_05_163058_add_image_and_description_to_products_table	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, name, price, stock, category, is_active, created_at, updated_at, image, description) FROM stdin;
1	Paleta de limón	15.00	50	Paleta	t	\N	\N	\N	\N
2	Paleta de fresa	15.00	40	Paleta	t	\N	\N	\N	\N
3	Paleta de chocolate	18.00	35	Paleta	t	\N	\N	\N	\N
4	Paleta de mango	15.00	30	Paleta	t	\N	\N	\N	\N
5	Paleta de tamarindo	18.00	25	Paleta	t	\N	\N	\N	\N
6	Helado de vainilla	25.00	30	Helado	t	\N	\N	\N	\N
7	Helado de chocolate	25.00	25	Helado	t	\N	\N	\N	\N
8	Helado de fresa	25.00	20	Helado	t	\N	\N	\N	\N
9	Agua de horchata	20.00	35	Agua	t	\N	\N	\N	\N
10	Agua de jamaica	20.00	30	Agua	t	\N	\N	\N	\N
11	Agua de limón	18.00	40	Agua	t	\N	\N	\N	\N
12	Bolis de mango	10.00	60	Bolis	t	\N	\N	\N	\N
13	Bolis de fresa	10.00	55	Bolis	t	\N	\N	\N	\N
\.


--
-- Data for Name: sale_details; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sale_details (id, sale_id, product_id, created_at, updated_at, qty, price_unit) FROM stdin;
1	1	3	2026-02-28 12:53:24	2026-03-05 19:20:24	2	18.00
2	1	4	2026-02-28 12:53:24	2026-03-05 19:20:24	2	15.00
3	1	11	2026-02-28 12:53:24	2026-03-05 19:20:24	1	18.00
4	2	3	2026-02-28 13:51:24	2026-03-05 19:20:24	2	18.00
5	2	9	2026-02-28 13:51:24	2026-03-05 19:20:24	1	20.00
6	3	5	2026-02-28 09:59:24	2026-03-05 19:20:24	2	18.00
7	4	2	2026-02-28 08:40:24	2026-03-05 19:20:24	2	15.00
8	4	7	2026-02-28 08:40:24	2026-03-05 19:20:24	3	25.00
9	5	1	2026-02-28 05:27:24	2026-03-05 19:20:24	3	15.00
10	5	3	2026-02-28 05:27:24	2026-03-05 19:20:24	2	18.00
11	5	9	2026-02-28 05:27:24	2026-03-05 19:20:24	1	20.00
12	6	3	2026-03-01 04:54:24	2026-03-05 19:20:24	1	18.00
13	7	1	2026-03-01 13:14:24	2026-03-05 19:20:24	2	15.00
14	7	3	2026-03-01 13:14:24	2026-03-05 19:20:24	1	18.00
15	7	4	2026-03-01 13:14:24	2026-03-05 19:20:24	2	15.00
16	7	12	2026-03-01 13:14:24	2026-03-05 19:20:24	1	10.00
17	8	2	2026-03-01 04:49:24	2026-03-05 19:20:24	3	15.00
18	8	4	2026-03-01 04:49:24	2026-03-05 19:20:24	2	15.00
19	8	13	2026-03-01 04:49:24	2026-03-05 19:20:24	1	10.00
20	9	2	2026-03-02 15:29:24	2026-03-05 19:20:24	2	15.00
21	10	1	2026-03-02 05:30:24	2026-03-05 19:20:24	1	15.00
22	10	3	2026-03-02 05:30:24	2026-03-05 19:20:24	1	18.00
23	11	2	2026-03-02 09:07:24	2026-03-05 19:20:24	2	15.00
24	11	8	2026-03-02 09:07:24	2026-03-05 19:20:24	3	25.00
25	11	12	2026-03-02 09:07:24	2026-03-05 19:20:24	1	10.00
26	12	7	2026-03-02 12:35:24	2026-03-05 19:20:24	1	25.00
27	13	1	2026-03-03 06:07:24	2026-03-05 19:20:24	3	15.00
28	13	3	2026-03-03 06:07:24	2026-03-05 19:20:24	1	18.00
29	13	5	2026-03-03 06:07:24	2026-03-05 19:20:24	2	18.00
30	13	9	2026-03-03 06:07:24	2026-03-05 19:20:24	1	20.00
31	14	1	2026-03-03 13:10:24	2026-03-05 19:20:24	3	15.00
32	14	5	2026-03-03 13:10:24	2026-03-05 19:20:24	1	18.00
33	14	9	2026-03-03 13:10:24	2026-03-05 19:20:24	1	20.00
34	14	11	2026-03-03 13:10:24	2026-03-05 19:20:24	2	18.00
35	15	3	2026-03-03 12:11:24	2026-03-05 19:20:24	2	18.00
36	15	5	2026-03-03 12:11:24	2026-03-05 19:20:24	1	18.00
37	15	10	2026-03-03 12:11:24	2026-03-05 19:20:24	1	20.00
38	15	13	2026-03-03 12:11:24	2026-03-05 19:20:24	1	10.00
39	16	1	2026-03-03 10:53:24	2026-03-05 19:20:24	2	15.00
40	16	6	2026-03-03 10:53:24	2026-03-05 19:20:24	1	25.00
41	16	11	2026-03-03 10:53:24	2026-03-05 19:20:24	3	18.00
42	17	6	2026-03-03 14:34:24	2026-03-05 19:20:24	1	25.00
43	18	6	2026-03-03 08:31:24	2026-03-05 19:20:24	2	25.00
44	18	8	2026-03-03 08:31:24	2026-03-05 19:20:24	3	25.00
45	18	11	2026-03-03 08:31:24	2026-03-05 19:20:24	2	18.00
46	19	1	2026-03-03 04:51:24	2026-03-05 19:20:24	3	15.00
47	19	6	2026-03-03 04:51:24	2026-03-05 19:20:24	1	25.00
48	20	2	2026-03-04 11:24:24	2026-03-05 19:20:24	3	15.00
49	20	4	2026-03-04 11:24:24	2026-03-05 19:20:24	2	15.00
50	20	6	2026-03-04 11:24:24	2026-03-05 19:20:24	3	25.00
51	21	2	2026-03-04 09:51:24	2026-03-05 19:20:24	2	15.00
52	21	4	2026-03-04 09:51:24	2026-03-05 19:20:24	1	15.00
53	21	7	2026-03-04 09:51:24	2026-03-05 19:20:24	2	25.00
54	22	1	2026-03-04 05:18:24	2026-03-05 19:20:24	2	15.00
55	22	13	2026-03-04 05:18:24	2026-03-05 19:20:24	3	10.00
56	23	4	2026-03-05 05:34:24	2026-03-05 19:20:24	1	15.00
57	23	5	2026-03-05 05:34:24	2026-03-05 19:20:24	1	18.00
58	24	6	2026-03-05 10:30:24	2026-03-05 19:20:24	3	25.00
59	25	3	2026-03-05 05:11:24	2026-03-05 19:20:24	1	18.00
60	26	1	2026-03-05 13:52:24	2026-03-05 19:20:24	3	15.00
61	26	2	2026-03-05 13:52:24	2026-03-05 19:20:24	1	15.00
62	26	4	2026-03-05 13:52:24	2026-03-05 19:20:24	2	15.00
63	26	13	2026-03-05 13:52:24	2026-03-05 19:20:24	2	10.00
64	27	3	2026-03-05 13:07:24	2026-03-05 19:20:24	3	18.00
65	27	8	2026-03-05 13:07:24	2026-03-05 19:20:24	2	25.00
66	27	10	2026-03-05 13:07:24	2026-03-05 19:20:24	3	20.00
67	28	12	2026-03-06 06:36:24	2026-03-05 19:20:24	2	10.00
68	29	3	2026-03-06 07:14:24	2026-03-05 19:20:24	3	18.00
69	29	4	2026-03-06 07:14:24	2026-03-05 19:20:24	2	15.00
70	29	10	2026-03-06 07:14:24	2026-03-05 19:20:24	2	20.00
71	30	2	2026-03-06 16:03:24	2026-03-05 19:20:24	3	15.00
72	30	7	2026-03-06 16:03:24	2026-03-05 19:20:24	3	25.00
73	31	2	2026-03-06 07:07:24	2026-03-05 19:20:24	1	15.00
74	32	11	2026-03-06 04:37:24	2026-03-05 19:20:24	1	18.00
75	33	8	2026-03-06 05:27:24	2026-03-05 19:20:24	2	25.00
76	34	3	2026-03-06 07:26:24	2026-03-05 19:20:24	2	18.00
\.


--
-- Data for Name: sales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sales (id, total, created_at, updated_at, user_id, sold_at) FROM stdin;
1	84.00	2026-02-28 12:53:24	2026-03-05 19:20:24	3	\N
2	56.00	2026-02-28 13:51:24	2026-03-05 19:20:24	2	\N
3	36.00	2026-02-28 09:59:24	2026-03-05 19:20:24	3	\N
4	105.00	2026-02-28 08:40:24	2026-03-05 19:20:24	2	\N
5	101.00	2026-02-28 05:27:24	2026-03-05 19:20:24	2	\N
6	18.00	2026-03-01 04:54:24	2026-03-05 19:20:24	2	\N
7	88.00	2026-03-01 13:14:24	2026-03-05 19:20:24	2	\N
8	85.00	2026-03-01 04:49:24	2026-03-05 19:20:24	3	\N
9	30.00	2026-03-02 15:29:24	2026-03-05 19:20:24	2	\N
10	33.00	2026-03-02 05:30:24	2026-03-05 19:20:24	2	\N
11	115.00	2026-03-02 09:07:24	2026-03-05 19:20:24	2	\N
12	25.00	2026-03-02 12:35:24	2026-03-05 19:20:24	2	\N
13	119.00	2026-03-03 06:07:24	2026-03-05 19:20:24	2	\N
14	119.00	2026-03-03 13:10:24	2026-03-05 19:20:24	2	\N
15	84.00	2026-03-03 12:11:24	2026-03-05 19:20:24	2	\N
16	109.00	2026-03-03 10:53:24	2026-03-05 19:20:24	3	\N
17	25.00	2026-03-03 14:34:24	2026-03-05 19:20:24	3	\N
18	161.00	2026-03-03 08:31:24	2026-03-05 19:20:24	2	\N
19	70.00	2026-03-03 04:51:24	2026-03-05 19:20:24	2	\N
20	150.00	2026-03-04 11:24:24	2026-03-05 19:20:24	3	\N
21	95.00	2026-03-04 09:51:24	2026-03-05 19:20:24	3	\N
22	60.00	2026-03-04 05:18:24	2026-03-05 19:20:24	3	\N
23	33.00	2026-03-05 05:34:24	2026-03-05 19:20:24	2	\N
24	75.00	2026-03-05 10:30:24	2026-03-05 19:20:24	3	\N
25	18.00	2026-03-05 05:11:24	2026-03-05 19:20:24	2	\N
26	110.00	2026-03-05 13:52:24	2026-03-05 19:20:24	3	\N
27	164.00	2026-03-05 13:07:24	2026-03-05 19:20:24	2	\N
28	20.00	2026-03-06 06:36:24	2026-03-05 19:20:24	2	\N
29	124.00	2026-03-06 07:14:24	2026-03-05 19:20:24	3	\N
30	120.00	2026-03-06 16:03:24	2026-03-05 19:20:24	3	\N
31	15.00	2026-03-06 07:07:24	2026-03-05 19:20:24	2	\N
32	18.00	2026-03-06 04:37:24	2026-03-05 19:20:24	3	\N
33	50.00	2026-03-06 05:27:24	2026-03-05 19:20:24	2	\N
34	36.00	2026-03-06 07:26:24	2026-03-05 19:20:24	3	\N
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
4nDNzfyDJDO9pb3m2q8sKBB83TJ4G0CiLJ2Zh6PK	\N	172.18.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoidjZTZk9wTUdWNTA4eHhGMkJld09KM0Q5VDMyaDY4THJKSGdwTVZCZyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovL2xvY2FsaG9zdDo4MDgwL3BhbmVsIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1772760977
\.


--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.settings (id, key, value, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role, is_active) FROM stdin;
1	Administrador	admin@gmail.com	\N	$2y$12$PA7LqgtYais7kpCWX4vIbeVtqeZEILTgPPUw.0dc5UvCiIfiqIcVW	\N	2026-03-05 19:20:24	2026-03-05 19:20:24	admin	t
2	Ian	ian@gmail.com	\N	$2y$12$0q/87cik9INsW1vo.TpdKe9r0UQDoDKcWmmozNCCza2EMO25/sKfO	\N	2026-03-05 19:20:24	2026-03-05 19:20:24	gerente	t
3	Jesus	jesus@gmail.com	\N	$2y$12$NNz1yfMBg14evbAS/zK1JurcByVaQP8GuuEM3POvQLRNREBRKSDvO	\N	2026-03-05 19:20:24	2026-03-05 19:20:24	vendedor	t
\.


--
-- Data for Name: weather_snapshots; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.weather_snapshots (id, date, city, temp, condition, raw, created_at, updated_at) FROM stdin;
\.


--
-- Name: audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.audit_logs_id_seq', 1, false);


--
-- Name: cash_registers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cash_registers_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 21, true);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 13, true);


--
-- Name: sale_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sale_details_id_seq', 76, true);


--
-- Name: sales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sales_id_seq', 34, true);


--
-- Name: settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.settings_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- Name: weather_snapshots_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.weather_snapshots_id_seq', 1, false);


--
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cash_registers cash_registers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cash_registers
    ADD CONSTRAINT cash_registers_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: sale_details sale_details_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_details
    ADD CONSTRAINT sale_details_pkey PRIMARY KEY (id);


--
-- Name: sales sales_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales
    ADD CONSTRAINT sales_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_unique UNIQUE (key);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: weather_snapshots weather_snapshots_date_city_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.weather_snapshots
    ADD CONSTRAINT weather_snapshots_date_city_unique UNIQUE (date, city);


--
-- Name: weather_snapshots weather_snapshots_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.weather_snapshots
    ADD CONSTRAINT weather_snapshots_pkey PRIMARY KEY (id);


--
-- Name: audit_logs_entity_type_entity_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX audit_logs_entity_type_entity_id_index ON public.audit_logs USING btree (entity_type, entity_id);


--
-- Name: audit_logs_module_action_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX audit_logs_module_action_index ON public.audit_logs USING btree (module, action);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: weather_snapshots_city_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX weather_snapshots_city_index ON public.weather_snapshots USING btree (city);


--
-- Name: weather_snapshots_date_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX weather_snapshots_date_index ON public.weather_snapshots USING btree (date);


--
-- Name: audit_logs audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: cash_registers cash_registers_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cash_registers
    ADD CONSTRAINT cash_registers_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: sale_details sale_details_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_details
    ADD CONSTRAINT sale_details_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: sale_details sale_details_sale_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_details
    ADD CONSTRAINT sale_details_sale_id_foreign FOREIGN KEY (sale_id) REFERENCES public.sales(id) ON DELETE CASCADE;


--
-- Name: sales sales_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sales
    ADD CONSTRAINT sales_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict 5OTjA4eWtRHYFb4mQUyz360bkpnFK55m7ROqoaxE5u5dG1M1gfjAkXNMHhcco8V

