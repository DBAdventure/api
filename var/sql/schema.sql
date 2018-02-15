--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;


--
-- Name: dragon_ball; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE dragon_ball (
    id integer NOT NULL,
    player_id integer,
    map_id integer,
    x integer,
    y integer,
    visible boolean NOT NULL
);


--
-- Name: dragon_ball_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE dragon_ball_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE dragon_ball_id_seq OWNED BY dragon_ball.id;


--
-- Name: guild; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE guild (
    id integer NOT NULL,
    created_by integer,
    name character varying(80) NOT NULL,
    short_name character varying(5) NOT NULL,
    history text NOT NULL,
    message text NOT NULL,
    zeni integer NOT NULL,
    enabled boolean DEFAULT false NOT NULL
);


--
-- Name: guild_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE guild_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE guild_id_seq OWNED BY guild.id;


--
-- Name: guild_player; Type: TABLE; Schema: public
--

CREATE TABLE guild_player (
    id integer NOT NULL,
    player_id integer NOT NULL,
    guild_id integer NOT NULL,
    rank_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    enabled boolean DEFAULT false NOT NULL,
    zeni integer NOT NULL
);


--
-- Name: guild_player_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE guild_player_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE guild_player_id_seq OWNED BY guild_player.id;


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY guild_player ALTER COLUMN id SET DEFAULT nextval('guild_player_id_seq'::regclass);


--
-- Name: guild_rank; Type: TABLE; Tablespace:
--

CREATE TABLE guild_rank (
    id integer NOT NULL,
    guild_id integer NOT NULL,
    name character varying(80) NOT NULL,
    role character varying(80) NOT NULL
);


--
-- Name: guild_rank_id_seq; Type: SEQUENCE
--

CREATE SEQUENCE guild_rank_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE guild_rank_id_seq OWNED BY guild_rank.id;


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY guild_rank ALTER COLUMN id SET DEFAULT nextval('guild_rank_id_seq'::regclass);


--
-- Name: event_type; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE event_type (
    id integer NOT NULL,
    name character varying(80) NOT NULL
);


--
-- Name: event_type_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE event_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: event_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE event_type_id_seq OWNED BY event_type.id;


--
-- Name: object; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE object (
    id integer NOT NULL,
    name character varying(50) NOT NULL,
    description text NOT NULL,
    price integer NOT NULL,
    image character varying(30) NOT NULL,
    weight numeric NOT NULL,
    bonus json NOT NULL,
    requirements json NOT NULL,
    type integer NOT NULL,
    enabled boolean DEFAULT false NOT NULL
);


--
-- Name: object_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE object_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: object_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE object_id_seq OWNED BY object.id;


--
-- Name: player_object; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE player_object (
    player_id integer NOT NULL,
    object_id integer NOT NULL,
    number integer NOT NULL,
    equipped boolean DEFAULT false NOT NULL
);



--
-- Name: player_event; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE player_event (
    id integer NOT NULL,
    player_id integer,
    target_id integer NOT NULL,
    message character varying(80) NOT NULL,
    parameters json NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    event_type_id integer NOT NULL
);


--
-- Name: player_event_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE player_event_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: player_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE player_event_id_seq OWNED BY player_event.id;


--
-- Name: guild_event; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE guild_event (
    id integer NOT NULL,
    player_id integer,
    guild_id integer NOT NULL,
    message character varying(80) NOT NULL,
    parameters json NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    event_type_id integer NOT NULL
);


--
-- Name: guild_event_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE guild_event_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: guild_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE guild_event_id_seq OWNED BY guild_event.id;


--
-- Name: bank; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE bank (
    player_id integer NOT NULL,
    zeni integer NOT NULL
);


--
-- Name: mail; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE mail (
    id integer NOT NULL,
    player_id integer NOT NULL,
    subject character varying(255) NOT NULL,
    template_name character varying(255) NOT NULL,
    parameters json NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    sent_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    message_sent text DEFAULT NULL
);


--
-- Name: mail_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE mail_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: mail_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE mail_id_seq OWNED BY mail.id;


--
-- Name: building; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE building (
    id integer NOT NULL,
    map_id integer NOT NULL,
    name character varying(80) NOT NULL,
    image character varying(50) NOT NULL,
    x integer NOT NULL,
    y integer NOT NULL,
    type integer NOT NULL,
    enabled boolean DEFAULT false NOT NULL
);


--
-- Name: building_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE building_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: building_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE building_id_seq OWNED BY building.id;


--
-- Name: map; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE map (
    id integer NOT NULL,
    name character varying(80) NOT NULL,
    max_x integer NOT NULL,
    max_y integer NOT NULL,
    type INT DEFAULT 0 NOT NULL
);


--
-- Name: map_bonus; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE map_bonus (
    id integer NOT NULL,
    name character varying(30) NOT NULL,
    bonus json NOT NULL,
    type integer NOT NULL
);


--
-- Name: map_bonus_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE map_bonus_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: map_bonus_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE map_bonus_id_seq OWNED BY map_bonus.id;


--
-- Name: map_object; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE map_object (
    id integer NOT NULL,
    x integer NOT NULL,
    y integer NOT NULL,
    number integer,
    map_object_type_id integer NOT NULL,
    map_id integer NOT NULL,
    object_id integer,
    extra json DEFAULT NULL
);


--
-- Name: map_object_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE map_object_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: map_object_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE map_object_id_seq OWNED BY map_object.id;


--
-- Name: map_object_type; Type: TABLE; Schema: public
--

CREATE TABLE map_object_type (
    id integer NOT NULL,
    name character varying(80) NOT NULL,
    image character varying(50) NOT NULL
);


--
-- Name: map_object_type_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE map_object_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: map_object_type_id_seq; Type: SEQUENCE OWNED BY
--

ALTER SEQUENCE map_object_type_id_seq OWNED BY map_object_type.id;


--
-- Name: map_box; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE map_box (
    map_id integer NOT NULL,
    x integer NOT NULL,
    y integer NOT NULL,
    map_image_id integer NOT NULL,
    map_bonus_id integer NOT NULL,
    damage integer NOT NULL
);


--
-- Name: map_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE map_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: map_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE map_id_seq OWNED BY map.id;


--
-- Name: map_image; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE map_image (
    id integer NOT NULL,
    name character varying(60) NOT NULL
);


--
-- Name: map_image_file; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE map_image_file (
    id integer NOT NULL,
    map_image_id integer NOT NULL,
    damage integer NOT NULL,
    period integer NOT NULL,
    file character varying(120) NOT NULL
);


--
-- Name: map_image_file_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE map_image_file_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: map_image_file_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE map_image_file_id_seq OWNED BY map_image_file.id;


--
-- Name: map_image_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE map_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: map_image_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE map_image_id_seq OWNED BY map_image.id;


--
-- Name: player; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE player (
    id integer NOT NULL,
    side_id integer NOT NULL,
    rank_id integer NOT NULL,
    race_id integer NOT NULL,
    map_id integer NOT NULL,
    username character varying(180) NOT NULL,
    username_canonical character varying(180) NOT NULL,
    email character varying(180) NOT NULL,
    email_canonical character varying(180) NOT NULL,
    enabled boolean DEFAULT false NOT NULL,
    salt character varying(255),
    password character varying(255) NOT NULL,
    last_login timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    confirmation_token character varying(180) DEFAULT NULL::character varying,
    password_requested_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    roles json NOT NULL,
    name character varying(50) NOT NULL,
    history text DEFAULT NULL,
    image character varying(10) NOT NULL,
    zeni integer NOT NULL,
    level integer NOT NULL,
    accuracy integer NOT NULL,
    agility integer NOT NULL,
    strength integer NOT NULL,
    resistance integer NOT NULL,
    skill integer NOT NULL,
    vision integer NOT NULL,
    analysis integer NOT NULL,
    intellect integer NOT NULL,
    ki integer NOT NULL,
    max_ki integer NOT NULL,
    health integer NOT NULL,
    max_health integer NOT NULL,
    action_points integer NOT NULL,
    fatigue_points integer NOT NULL,
    movement_points integer NOT NULL,
    battle_points integer NOT NULL,
    ip character varying(15) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    x integer NOT NULL,
    y integer NOT NULL,
    forbidden_teleport character varying(2),
    target_id integer,
    side_points integer DEFAULT 0 NOT NULL,
    skill_points integer DEFAULT 0 NOT NULL,
    death_count integer DEFAULT 0 NOT NULL,
    nb_kill_good integer DEFAULT 0 NOT NULL,
    nb_hit_good integer DEFAULT 0 NOT NULL,
    nb_damage_good integer DEFAULT 0 NOT NULL,
    nb_kill_bad integer DEFAULT 0 NOT NULL,
    nb_hit_bad integer DEFAULT 0 NOT NULL,
    nb_damage_bad integer DEFAULT 0 NOT NULL,
    nb_kill_npc integer DEFAULT 0 NOT NULL,
    nb_hit_npc integer DEFAULT 0 NOT NULL,
    nb_damage_npc integer DEFAULT 0 NOT NULL,
    nb_kill_hq integer DEFAULT 0 NOT NULL,
    nb_hit_hq integer DEFAULT 0 NOT NULL,
    nb_damage_hq integer DEFAULT 0 NOT NULL,
    nb_stolen_zeni integer DEFAULT 0 NOT NULL,
    nb_action_stolen_zeni integer DEFAULT 0 NOT NULL,
    nb_dodge integer DEFAULT 0 NOT NULL,
    nb_wanted integer DEFAULT 0 NOT NULL,
    nb_analysis integer DEFAULT 0 NOT NULL,
    nb_spell integer DEFAULT 0 NOT NULL,
    nb_health_given integer DEFAULT 0 NOT NULL,
    nb_total_health_given integer DEFAULT 0 NOT NULL,
    nb_slap_taken integer DEFAULT 0 NOT NULL,
    nb_slap_given integer DEFAULT 0 NOT NULL,
    betrayals integer DEFAULT 0 NOT NULL,
    head_price integer DEFAULT 0 NOT NULL,
    action_updated_at timestamp(0) without time zone NOT NULL,
    movement_updated_at timestamp(0) without time zone NOT NULL,
    ki_updated_at timestamp(0) without time zone NOT NULL,
    fatigue_updated_at timestamp(0) without time zone NOT NULL
);


--
-- Name: player_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE player_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: player_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE player_id_seq OWNED BY player.id;


--
-- Name: race; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE race (
    id integer NOT NULL,
    name character varying(50) NOT NULL
);


--
-- Name: race_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE race_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: race_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE race_id_seq OWNED BY race.id;


--
-- Name: rank; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE rank (
    id integer NOT NULL,
    race_id integer NOT NULL,
    level integer NOT NULL,
    name character varying(50) NOT NULL
);


--
-- Name: rank_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE rank_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rank_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE rank_id_seq OWNED BY rank.id;


--
-- Name: side; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE side (
    id integer NOT NULL,
    name character varying(50) NOT NULL
);


--
-- Name: side_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE side_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: side_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE side_id_seq OWNED BY side.id;


--
-- Name: inbox; Type: TABLE; Tablespace:
--

CREATE TABLE inbox (
    id integer NOT NULL,
    recipient_id integer NOT NULL,
    sender_id integer NOT NULL,
    status integer NOT NULL,
    subject character varying(255) NOT NULL,
    message text NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    sender_directory character varying(20) NOT NULL,
    recipient_directory character varying(20) NOT NULL
);


--
-- Name: inbox_id_seq; Type: SEQUENCE;


--

CREATE SEQUENCE inbox_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: inbox_id_seq; Type: SEQUENCE OWNED BY;


--

ALTER SEQUENCE inbox_id_seq OWNED BY inbox.id;


--
-- Name: news; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE news (
    id integer NOT NULL,
    created_by integer,
    subject character varying(255) NOT NULL,
    message text NOT NULL,
    image character varying(80) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    enabled boolean DEFAULT false NOT NULL
);


--
-- Name: news_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: news_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE news_id_seq OWNED BY news.id;


--
-- Name: spell; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE spell (
    id integer NOT NULL,
    name character varying(80) NOT NULL,
    requirements json NOT NULL,
    bonus json NOT NULL,
    price integer NOT NULL,
    distance integer NOT NULL,
    damages integer NOT NULL,
    type integer NOT NULL,
    race_id integer NOT NULL
);


--
-- Name: spell_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE spell_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: spell_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE spell_id_seq OWNED BY spell.id;


--
-- Name: player_spell; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE player_spell (
    id integer NOT NULL,
    player_id integer NOT NULL,
    spell_id integer NOT NULL
);


--
-- Name: player_spell_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE player_spell_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: player_spell_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE player_spell_id_seq OWNED BY player_spell.id;


--
-- Name: player_spell_effect; Type: TABLE; Schema: public; Tablespace:
--

CREATE TABLE player_spell_effect (
    id integer NOT NULL,
    player_spell_id integer NOT NULL,
    target_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    times_used integer
);


--
-- Name: player_spell_effect_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE player_spell_effect_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: player_spell_effect_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE player_spell_effect_id_seq OWNED BY player_spell_effect.id;


--
-- Name: npc_object; Type: TABLE; Schema: public
--

CREATE TABLE npc_object (
    id integer NOT NULL,
    name character varying(80) NOT NULL,
    luck integer NOT NULL
);


ALTER TABLE npc_object OWNER TO dba;


--
-- Name: npc_object_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE npc_object_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: npc_object_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE npc_object_id_seq OWNED BY npc_object.id;


--
-- Name: quest; Type: TABLE; Schema: public
--

CREATE TABLE quest (
    id integer NOT NULL,
    map_id integer NOT NULL,
    name character varying(80) NOT NULL,
    npc_name character varying(80) NOT NULL,
    image character varying(50) NOT NULL,
    history text NOT NULL,
    x integer NOT NULL,
    y integer NOT NULL,
    enabled boolean DEFAULT false NOT NULL,
    lifetime integer NOT NULL,
    gain_battle_points integer NOT NULL,
    gain_zeni integer NOT NULL,
    requirements json DEFAULT '{}' NOT NULL,
    on_accepted JSON DEFAULT '{}' NOT NULL,
    on_completed JSON DEFAULT '{}' NOT NULL,
    on_finished JSON DEFAULT '{}' NOT NULL
);


--
-- Name: quest_gain_object; Type: TABLE; Schema: public
--

CREATE TABLE quest_gain_object (
    quest_id integer NOT NULL,
    object_id integer NOT NULL,
    number integer NOT NULL
);


--
-- Name: quest_id_seq; Type: SEQUENCE; Schema: public
--

CREATE SEQUENCE quest_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: quest_id_seq; Type: SEQUENCE OWNED BY; Schema: public
--

ALTER SEQUENCE quest_id_seq OWNED BY quest.id;


--
-- Name: quest_npc; Type: TABLE; Schema: public
--

CREATE TABLE quest_npc (
    quest_id integer NOT NULL,
    race_id integer NOT NULL,
    number integer NOT NULL
);


--
-- Name: quest_npc_object; Type: TABLE; Schema: public
--

CREATE TABLE quest_npc_object (
    quest_id integer NOT NULL,
    npc_object_id integer NOT NULL,
    number integer NOT NULL
);


--
-- Name: quest_object; Type: TABLE; Schema: public
--

CREATE TABLE quest_object (
    quest_id integer NOT NULL,
    object_id integer NOT NULL,
    number integer NOT NULL
);


--
-- Name: npc_object_race; Type: TABLE; Schema: public
--

CREATE TABLE npc_object_race (
    npc_object_id integer NOT NULL,
    race_id integer NOT NULL
);


--
-- Name: player_quest; Type: TABLE; Schema: public
--

CREATE TABLE player_quest (
    player_id integer NOT NULL,
    quest_id integer NOT NULL,
    status integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    npc_objects JSON NOT NULL,
    npcs JSON NOT NULL
);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY dragon_ball ALTER COLUMN id SET DEFAULT nextval('dragon_ball_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY news ALTER COLUMN id SET DEFAULT nextval('news_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT;


--

ALTER TABLE ONLY inbox ALTER COLUMN id SET DEFAULT nextval('inbox_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY guild ALTER COLUMN id SET DEFAULT nextval('guild_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY mail ALTER COLUMN id SET DEFAULT nextval('mail_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY building ALTER COLUMN id SET DEFAULT nextval('building_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY event_type ALTER COLUMN id SET DEFAULT nextval('event_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY map ALTER COLUMN id SET DEFAULT nextval('map_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY map_bonus ALTER COLUMN id SET DEFAULT nextval('map_bonus_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY map_image ALTER COLUMN id SET DEFAULT nextval('map_image_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY map_image_file ALTER COLUMN id SET DEFAULT nextval('map_image_file_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY map_object ALTER COLUMN id SET DEFAULT nextval('map_object_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY map_object_type ALTER COLUMN id SET DEFAULT nextval('map_object_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY object ALTER COLUMN id SET DEFAULT nextval('object_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY spell ALTER COLUMN id SET DEFAULT nextval('spell_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY player ALTER COLUMN id SET DEFAULT nextval('player_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY player_event ALTER COLUMN id SET DEFAULT nextval('player_event_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY guild_event ALTER COLUMN id SET DEFAULT nextval('guild_event_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY race ALTER COLUMN id SET DEFAULT nextval('race_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY rank ALTER COLUMN id SET DEFAULT nextval('rank_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY side ALTER COLUMN id SET DEFAULT nextval('side_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY player_spell ALTER COLUMN id SET DEFAULT nextval('player_spell_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT
--

ALTER TABLE ONLY player_spell_effect ALTER COLUMN id SET DEFAULT nextval('player_spell_effect_id_seq'::regclass);


--
-- Name: quest id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY quest ALTER COLUMN id SET DEFAULT nextval('quest_id_seq'::regclass);


--
-- Name: dragon_ball_pkey; Type: CONSTRAINT; Schema: public; Tablespace:
--

ALTER TABLE ONLY dragon_ball
    ADD CONSTRAINT dragon_ball_pkey PRIMARY KEY (id);


--
-- Name: news_pkey; Type: CONSTRAINT; Schema: public; Tablespace:
--

ALTER TABLE ONLY news
    ADD CONSTRAINT news_pkey PRIMARY KEY (id);


--
-- Name: inbox_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY inbox
    ADD CONSTRAINT inbox_pkey PRIMARY KEY (id);


--
-- Name: guild_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY guild
    ADD CONSTRAINT guild_pkey PRIMARY KEY (id);


--
-- Name: guild_rank_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY guild_rank
    ADD CONSTRAINT guild_rank_pkey PRIMARY KEY (id);


--
-- Name: bank_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY bank
    ADD CONSTRAINT bank_pkey PRIMARY KEY (player_id);


--
-- Name: mail_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY mail
    ADD CONSTRAINT mail_pkey PRIMARY KEY (id);


--
-- Name: building_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY building
    ADD CONSTRAINT building_pkey PRIMARY KEY (id);


--
-- Name: event_type_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY event_type
    ADD CONSTRAINT event_type_pkey PRIMARY KEY (id);


--
-- Name: map_bonus_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map_bonus
    ADD CONSTRAINT map_bonus_pkey PRIMARY KEY (id);


--
-- Name: map_box_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map_box
    ADD CONSTRAINT map_box_pkey PRIMARY KEY (map_id, x, y);


--
-- Name: map_image_file_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map_image_file
    ADD CONSTRAINT map_image_file_pkey PRIMARY KEY (id);


--
-- Name: map_image_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map_image
    ADD CONSTRAINT map_image_pkey PRIMARY KEY (id);


--
-- Name: map_object_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_pkey PRIMARY KEY (id);


--
-- Name: map_object_type_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map_object_type
    ADD CONSTRAINT map_object_type_pkey PRIMARY KEY (id);


--
-- Name: map_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY map
    ADD CONSTRAINT map_pkey PRIMARY KEY (id);


--
-- Name: object_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY object
    ADD CONSTRAINT object_pkey PRIMARY KEY (id);


--
-- Name: player_event_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY player_event
    ADD CONSTRAINT player_event_pkey PRIMARY KEY (id);


--
-- Name: guild_event_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY guild_event
    ADD CONSTRAINT guild_event_pkey PRIMARY KEY (id);


--
-- Name: player_object_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY player_object
    ADD CONSTRAINT player_object_pkey PRIMARY KEY (player_id, object_id);


--
-- Name: player_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY player
    ADD CONSTRAINT player_pkey PRIMARY KEY (id);


--
-- Name: race_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY race
    ADD CONSTRAINT race_pkey PRIMARY KEY (id);


--
-- Name: rank_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY rank
    ADD CONSTRAINT rank_pkey PRIMARY KEY (id);


--
-- Name: side_pkey; Type: CONSTRAINT; Tablespace:
--

ALTER TABLE ONLY side
    ADD CONSTRAINT side_pkey PRIMARY KEY (id);


--
-- Name: guild_player_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY guild_player
    ADD CONSTRAINT guild_player_pkey PRIMARY KEY (id);


--
-- Name: spell_pkey; Type: CONSTRAINT; Schema: public; Tablespace:
--

ALTER TABLE ONLY spell
    ADD CONSTRAINT spell_pkey PRIMARY KEY (id);


--
-- Name: player_spell_pkey; Type: CONSTRAINT; Schema: public; Tablespace:
--

ALTER TABLE ONLY player_spell
    ADD CONSTRAINT player_spell_pkey PRIMARY KEY (id);


--
-- Name: player_spell_effect_pkey; Type: CONSTRAINT; Schema: public; Tablespace:
--

ALTER TABLE ONLY player_spell_effect
    ADD CONSTRAINT player_spell_effect_pkey PRIMARY KEY (id);


--
-- Name: quest_gain_object quest_gain_object_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_gain_object
    ADD CONSTRAINT quest_gain_object_pkey PRIMARY KEY (quest_id, object_id);


--
-- Name: quest_npc_object quest_npc_object_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_npc_object
    ADD CONSTRAINT quest_npc_object_pkey PRIMARY KEY (quest_id, npc_object_id);


--
-- Name: quest_npc quest_npc_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_npc
    ADD CONSTRAINT quest_npc_pkey PRIMARY KEY (quest_id, race_id);


--
-- Name: quest_object quest_object_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_object
    ADD CONSTRAINT quest_object_pkey PRIMARY KEY (quest_id, object_id);


--
-- Name: quest quest_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest
    ADD CONSTRAINT quest_pkey PRIMARY KEY (id);


--
-- Name: npc_object id; Type: DEFAULT; Schema: public
--

ALTER TABLE ONLY npc_object ALTER COLUMN id SET DEFAULT nextval('npc_object_id_seq'::regclass);


--
-- Name: news_created_by; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX news_created_by ON news USING btree (created_by);


--
-- Name: inbox_recipient_id
--

CREATE INDEX inbox_recipient_id ON inbox USING btree (recipient_id);


--
-- Name: inbox_sender_id
--

CREATE INDEX inbox_sender_id ON inbox USING btree (sender_id);


--
-- Name: guild_player_guild; Type: INDEX; Schema: public
--

CREATE INDEX guild_player_guild ON guild_player USING btree (guild_id);


--
-- Name: idx_fc65835199e6f5df; Type: INDEX; Schema: public
--

CREATE INDEX idx_fc65835199e6f5df ON player_quest (player_id);

--
-- Name: idx_fc658351209e9ef4; Type: INDEX; Schema: public
--

CREATE INDEX idx_fc658351209e9ef4 ON player_quest (quest_id);

--
-- Name: idx_43daa8899b267ee1; Type: INDEX; Schema: public
--

CREATE INDEX idx_43daa8899b267ee1 ON npc_object_race (npc_object_id);

--
-- Name: idx_43daa8896e59d40d; Type: INDEX; Schema: public
--

CREATE INDEX idx_43daa8896e59d40d ON npc_object_race (race_id);
--
-- Name: map_box_unique
--

CREATE UNIQUE INDEX map_box_unique ON map_box (map_id, x, y);


--
-- Name: map_image_file_file
--

CREATE UNIQUE INDEX map_image_file_file ON map_image_file (file);


--
-- Name: player_username
--

CREATE UNIQUE INDEX player_username ON player (username);


--
-- Name: player_email
--

CREATE UNIQUE INDEX player_email ON player (email);


--
-- Name: guild_player_player
--

CREATE UNIQUE INDEX guild_player_player ON guild_player USING btree (player_id);


--
-- Name: guild_player_rank
--

CREATE INDEX guild_player_rank ON guild_player USING btree (rank_id);


--
-- Name: guild_rank_guild
--

CREATE INDEX guild_rank_guild ON guild_rank USING btree (guild_id);


--
-- Name: guild_rank_name
--

CREATE UNIQUE INDEX guild_rank_name ON guild_rank USING btree (name, guild_id);


--
-- Name: guild_name; Type: INDEX; Schema: public; Tablespace:
--

CREATE UNIQUE INDEX guild_name ON guild USING btree (name);


--
-- Name: guild_short_name; Type: INDEX; Schema: public; Tablespace:
--

CREATE UNIQUE INDEX guild_short_name ON guild USING btree (short_name);


--
-- Name: idx_75407dabde12ab56; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX idx_75407dabde12ab56 ON guild USING btree (created_by);


--
-- Name: building_map_id;


--

CREATE INDEX building_map_id ON building USING btree (map_id);



--
-- Name: event_type_name;



CREATE INDEX event_type_name ON event_type USING btree (name);


--
-- Name: rank_race_id;


--

CREATE INDEX rank_race_id ON rank USING btree (race_id);


--
-- Name: map_bonus_id;


--

CREATE INDEX map_bonus_id ON map_box USING btree (map_bonus_id);


--
-- Name: map_bonus_name;


--

CREATE UNIQUE INDEX map_bonus_name ON map_bonus USING btree (name);


--
-- Name: map_image_file_id;


--

CREATE INDEX map_image_file_id ON map_image_file USING btree (map_image_id);


--
-- Name: map_image_id;


--

CREATE INDEX map_image_id ON map_box USING btree (map_image_id);


--
-- Name: map_image_name;


--

CREATE UNIQUE INDEX map_image_name ON map_image USING btree (name);


--
-- Name: map_object_map_id;


--

CREATE INDEX map_object_map_id ON map_object USING btree (map_id);


--
-- Name: map_object_map_object_type_id;


--

CREATE INDEX map_object_map_object_type_id ON map_object USING btree (map_object_type_id);


--
-- Name: map_object_object_id;


--

CREATE INDEX map_object_object_id ON map_object USING btree (object_id);


--
-- Name: map_object_type_name;


--

CREATE INDEX map_object_type_name ON map_object_type USING btree (name);


--
-- Name: object_name;


--

CREATE UNIQUE INDEX object_name ON object USING btree (name);


--
-- Name: player_confirmation_token;


--

CREATE UNIQUE INDEX player_confirmation_token ON player USING btree (confirmation_token);


--
-- Name: player_email_canonical;


--

CREATE UNIQUE INDEX player_email_canonical ON player USING btree (email_canonical);


--
-- Name: player_map_id;


--

CREATE INDEX player_map_id ON player USING btree (map_id);


--
-- Name: player_name;


--

CREATE UNIQUE INDEX player_name ON player USING btree (name);


--
-- Name: player_race_id;


--

CREATE INDEX player_race_id ON player USING btree (race_id);


--
-- Name: player_rank_id;


--

CREATE INDEX player_rank_id ON player USING btree (rank_id);


--
-- Name: player_side_id;


--

CREATE INDEX player_side_id ON player USING btree (side_id);


--
-- Name: player_target_id;


--

CREATE INDEX player_target_id ON player USING btree (target_id);


--
-- Name: player_username_canonical;


--

CREATE UNIQUE INDEX player_username_canonical ON player USING btree (username_canonical);


--
-- Name: race_name;


--

CREATE UNIQUE INDEX race_name ON race USING btree (name);


--
-- Name: rank_name;


--

CREATE UNIQUE INDEX rank_name ON rank USING btree (name);


--
-- Name: rank_race_id_level;


--

CREATE UNIQUE INDEX rank_race_id_level ON rank USING btree (race_id, level);


--
-- Name: side_name;


--

CREATE UNIQUE INDEX side_name ON side USING btree (name);


--
-- Name: player_event_target
--

CREATE INDEX player_event_target ON player_event USING btree (target_id);


--
-- Name: player_event_player
--

CREATE INDEX player_event_player ON player_event USING btree (player_id);


--
-- Name: guild_event_guild
--

CREATE INDEX guild_event_guild ON guild_event USING btree (guild_id);


--
-- Name: guild_event_player
--

CREATE INDEX guild_event_player ON guild_event USING btree (player_id);


--
-- Name: spell_name_race_id; Type: INDEX; Schema: public; Tablespace:
--

CREATE UNIQUE INDEX spell_name_race_id ON spell USING btree (name, race_id);


--
-- Name: player_spell_player; Type: INDEX; Schema: public; Tablespace:
--

CREATE UNIQUE INDEX player_spell_player_spell ON player_spell USING btree (player_id, spell_id);


--
-- Name: player_spell_spell; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX player_spell_spell ON player_spell (spell_id);


--
-- Name: player_spell_spell; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX player_spell_player ON player_spell (player_id);


--
-- Name: player_spell_effect_spell; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX player_spell_effect_spell ON player_spell_effect (player_spell_id);


--
-- Name: player_spell_effect_target_id; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX player_spell_effect_target_id ON player_spell_effect (target_id);


--
-- Name: player_spell_effect_target_player_spell; Type: INDEX; Schema: public; Tablespace:
--

CREATE UNIQUE INDEX player_spell_effect_target_player_spell ON player_spell_effect USING btree (target_id, player_spell_id);


--
-- Name: dragon_ball_map_id; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX dragon_ball_map_id ON dragon_ball USING btree (map_id);


--
-- Name: dragon_ball_player_id; Type: INDEX; Schema: public; Tablespace:
--

CREATE INDEX dragon_ball_player_id ON dragon_ball USING btree (player_id);


--
-- Name: quest_gain_object_object_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_gain_object_object_id ON quest_gain_object USING btree (object_id);


--
-- Name: quest_gain_object_quest_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_gain_object_quest_id ON quest_gain_object USING btree (quest_id);


--
-- Name: quest_map_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_map_id ON quest USING btree (map_id);


--
-- Name: quest_npc_npc_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_npc_race_id ON quest_npc USING btree (race_id);


--
-- Name: quest_npc_object_npc_object_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_npc_object_npc_object_id ON quest_npc_object USING btree (npc_object_id);


--
-- Name: quest_npc_object_quest_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_npc_object_quest_id ON quest_npc_object USING btree (quest_id);


--
-- Name: quest_npc_quest_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_npc_quest_id ON quest_npc USING btree (quest_id);


--
-- Name: quest_object_object_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_object_object_id ON quest_object USING btree (object_id);


--
-- Name: quest_object_quest_id; Type: INDEX; Schema: public
--

CREATE INDEX quest_object_quest_id ON quest_object USING btree (quest_id);


--
-- Name: fk_75407dabde12ab56
--

ALTER TABLE ONLY guild
    ADD CONSTRAINT fk_75407dabde12ab56 FOREIGN KEY (created_by) REFERENCES player(id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_97280c8a5f2131ef
--

ALTER TABLE ONLY guild_player
    ADD CONSTRAINT fk_97280c8a5f2131ef FOREIGN KEY (guild_id) REFERENCES guild(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_97280c8a99e6f5df
--

ALTER TABLE ONLY guild_player
    ADD CONSTRAINT fk_97280c8a99e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_97280c8a99e6f5df
--

ALTER TABLE ONLY guild_player
    ADD CONSTRAINT fk_97280c8a7616678f FOREIGN KEY (rank_id) REFERENCES guild_rank (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_29f140b75f57134a;


--

ALTER TABLE ONLY map_box
    ADD CONSTRAINT fk_29f140b75f57134a FOREIGN KEY (map_image_id) REFERENCES map_image(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_29f140b7ba66041;


--

ALTER TABLE ONLY map_box
    ADD CONSTRAINT fk_29f140b753c55f64 FOREIGN KEY (map_id) REFERENCES map (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_29f140b7ba66041;


--

ALTER TABLE ONLY map_box
    ADD CONSTRAINT fk_29f140b7ba66041 FOREIGN KEY (map_bonus_id) REFERENCES map_bonus(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_37ad3d32232d562b;


--

ALTER TABLE ONLY player_object
    ADD CONSTRAINT fk_37ad3d32232d562b FOREIGN KEY (object_id) REFERENCES object(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_37ad3d3299e6f5df;


--

ALTER TABLE ONLY player_object
    ADD CONSTRAINT fk_37ad3d3299e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_6bda99805f57134a;


--

ALTER TABLE ONLY map_image_file
    ADD CONSTRAINT fk_6bda99805f57134a FOREIGN KEY (map_image_id) REFERENCES map_image(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_84dc71e1158e0b66;


--

ALTER TABLE ONLY player_event
    ADD CONSTRAINT fk_84dc71e1158e0b66 FOREIGN KEY (target_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_84dc71e199e6f5df;


--

ALTER TABLE ONLY player_event
    ADD CONSTRAINT fk_84dc71e199e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_84dc71e199e6f5df;


--

ALTER TABLE ONLY player_event
    ADD CONSTRAINT fk_84dc71e1401b253c FOREIGN KEY (event_type_id) REFERENCES event_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_a21d1ee99e6f5df;


--

ALTER TABLE ONLY guild_event
    ADD CONSTRAINT fk_a21d1ee99e6f5df FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_a21d1ee5f2131ef;


--

ALTER TABLE ONLY guild_event
    ADD CONSTRAINT fk_a21d1ee5f2131ef FOREIGN KEY (guild_id) REFERENCES guild (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_a21d1ee401b253c;


--

ALTER TABLE ONLY guild_event
    ADD CONSTRAINT fk_a21d1ee401b253c FOREIGN KEY (event_type_id) REFERENCES event_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_8879e8e56e59d40d;


--

ALTER TABLE ONLY rank
    ADD CONSTRAINT fk_8879e8e56e59d40d FOREIGN KEY (race_id) REFERENCES race(id);


--
-- Name: fk_98197a65158e0b66;


--

ALTER TABLE ONLY player
    ADD CONSTRAINT fk_98197a65158e0b66 FOREIGN KEY (target_id) REFERENCES player(id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_98197a6553c55f64;


--

ALTER TABLE ONLY player
    ADD CONSTRAINT fk_98197a6553c55f64 FOREIGN KEY (map_id) REFERENCES map(id);


--
-- Name: fk_98197a656e59d40d;


--

ALTER TABLE ONLY player
    ADD CONSTRAINT fk_98197a656e59d40d FOREIGN KEY (race_id) REFERENCES race(id);


--
-- Name: fk_98197a657616678f;


--

ALTER TABLE ONLY player
    ADD CONSTRAINT fk_98197a657616678f FOREIGN KEY (rank_id) REFERENCES rank(id);


--
-- Name: fk_98197a65965d81c4;


--

ALTER TABLE ONLY player
    ADD CONSTRAINT fk_98197a65965d81c4 FOREIGN KEY (side_id) REFERENCES side(id);


--
-- Name: fk_d860bf7a99e6f5df;


--

ALTER TABLE ONLY bank
    ADD CONSTRAINT fk_d860bf7a99e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_5126ac4899e6f5df;


--

ALTER TABLE ONLY mail
    ADD CONSTRAINT fk_5126ac4899e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_e16f61d453c55f64;


--

ALTER TABLE ONLY building
    ADD CONSTRAINT fk_e16f61d453c55f64 FOREIGN KEY (map_id) REFERENCES map(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_ec2970d0232d562b;


--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT fk_ec2970d0232d562b FOREIGN KEY (object_id) REFERENCES object(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_ec2970d053c55f64;


--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT fk_ec2970d053c55f64 FOREIGN KEY (map_id) REFERENCES map(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_ec2970d096f6b5f9;


--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT fk_ec2970d096f6b5f9 FOREIGN KEY (map_object_type_id) REFERENCES map_object_type(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_692c4e4b5f2131ef; Type: FK CONSTRAINT;


--
ALTER TABLE ONLY guild_rank
    ADD CONSTRAINT fk_692c4e4b5f2131ef FOREIGN KEY (guild_id) REFERENCES guild(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_7e11f339e92f8f78; Type: FK CONSTRAINT;


--

ALTER TABLE ONLY inbox
    ADD CONSTRAINT fk_7e11f339e92f8f78 FOREIGN KEY (recipient_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_1dd39950de12ab56; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY news
    ADD CONSTRAINT fk_1dd39950de12ab56 FOREIGN KEY (created_by) REFERENCES player(id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_7e11f339f624b39d; Type: FK CONSTRAINT;


--

ALTER TABLE ONLY inbox
    ADD CONSTRAINT fk_7e11f339f624b39d FOREIGN KEY (sender_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_d03fcd8d6e59d40d
--

ALTER TABLE ONLY spell
    ADD CONSTRAINT fk_d03fcd8d6e59d40d FOREIGN KEY (race_id) REFERENCES race(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_8efc36c5414988ed
--

ALTER TABLE ONLY player_spell_effect
    ADD CONSTRAINT fk_8efc36c5414988ed FOREIGN KEY (player_spell_id) REFERENCES player_spell(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_8efc36c5158e0b66
--

ALTER TABLE ONLY player_spell_effect
    ADD CONSTRAINT fk_8efc36c5158e0b66 FOREIGN KEY (target_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_6f4db6cb99e6f5df
--

ALTER TABLE ONLY player_spell
    ADD CONSTRAINT fk_6f4db6cb99e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_6f4db6cb479ec90d
--

ALTER TABLE ONLY player_spell
    ADD CONSTRAINT fk_6f4db6cb479ec90d FOREIGN KEY (spell_id) REFERENCES spell(id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_4835a0cd99e6f5df
--

ALTER TABLE ONLY dragon_ball
    ADD CONSTRAINT fk_4835a0cd99e6f5df FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: fk_4835a0cd53c55f64
--

ALTER TABLE ONLY dragon_ball
    ADD CONSTRAINT fk_4835a0cd53c55f64 FOREIGN KEY (map_id) REFERENCES map(id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE;


--
-- Name: npc_object npc_object_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY npc_object
    ADD CONSTRAINT npc_object_pkey PRIMARY KEY (id);


--
-- Name: player_quest player_quest_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY player_quest
    ADD CONSTRAINT player_quest_pkey PRIMARY KEY (player_id, quest_id);


--
-- Name: npc_object_race npc_object_race_pkey; Type: CONSTRAINT; Schema: public
--

ALTER TABLE ONLY npc_object_race
    ADD CONSTRAINT npc_object_race_pkey PRIMARY KEY (npc_object_id, race_id);


--
-- Name: quest fk_4317f81753c55f64; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest
    ADD CONSTRAINT fk_4317f81753c55f64 FOREIGN KEY (map_id) REFERENCES map(id) ON DELETE CASCADE;


--
-- Name: quest_object fk_5147c91a209e9ef4; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_object
    ADD CONSTRAINT fk_5147c91a209e9ef4 FOREIGN KEY (quest_id) REFERENCES quest(id) ON DELETE CASCADE;


--
-- Name: quest_object fk_5147c91a232d562b; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_object
    ADD CONSTRAINT fk_5147c91a232d562b FOREIGN KEY (object_id) REFERENCES object(id) ON DELETE CASCADE;


--
-- Name: quest_npc fk_97a8b624209e9ef4; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_npc
    ADD CONSTRAINT fk_97a8b624209e9ef4 FOREIGN KEY (quest_id) REFERENCES quest(id) ON DELETE CASCADE;


--
-- Name: quest_npc fk_97a8b624232d562b; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_npc
    ADD CONSTRAINT fk_97a8b624232d562b FOREIGN KEY (race_id) REFERENCES race(id) ON DELETE CASCADE;


--
-- Name: quest_npc_object fk_c63facc2209e9ef4; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_npc_object
    ADD CONSTRAINT fk_c63facc2209e9ef4 FOREIGN KEY (quest_id) REFERENCES quest(id) ON DELETE CASCADE;


--
-- Name: quest_npc_object fk_c63facc29b267ee1; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_npc_object
    ADD CONSTRAINT fk_c63facc29b267ee1 FOREIGN KEY (npc_object_id) REFERENCES npc_object(id) ON DELETE CASCADE;


--
-- Name: quest_gain_object fk_e24ede89209e9ef4; Type: FK CONSTRAINT; Schema: public
--

ALTER TABLE ONLY quest_gain_object
    ADD CONSTRAINT fk_e24ede89209e9ef4 FOREIGN KEY (quest_id) REFERENCES quest (id) ON DELETE CASCADE;


--
-- Name: quest_gain_object fk_e24ede89232d562b; Type: FK CONSTRAINT; Schema: public
--


ALTER TABLE ONLY quest_gain_object
    ADD CONSTRAINT fk_e24ede89232d562b FOREIGN KEY (object_id) REFERENCES object (id) ON DELETE CASCADE;


--
-- Name: player_quest fk_fc65835199e6f5df; Type: FK CONSTRAINT; Schema: public
--


ALTER TABLE ONLY player_quest
    ADD CONSTRAINT fk_fc65835199e6f5df FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE;


--
-- Name: player_quest fk_fc658351209e9ef4; Type: FK CONSTRAINT; Schema: public
--


ALTER TABLE ONLY player_quest
    ADD CONSTRAINT fk_fc658351209e9ef4 FOREIGN KEY (quest_id) REFERENCES quest (id) ON DELETE CASCADE;


--
-- Name: npc_object_race fk_43daa8899b267ee1; Type: FK CONSTRAINT; Schema: public
--


ALTER TABLE ONLY npc_object_race
    ADD CONSTRAINT fk_43daa8899b267ee1 FOREIGN KEY (npc_object_id) REFERENCES npc_object (id) ON DELETE CASCADE;


--
-- Name: npc_object_race fk_43daa8896e59d40d; Type: FK CONSTRAINT; Schema: public
--


ALTER TABLE ONLY npc_object_race
    ADD CONSTRAINT fk_43daa8896e59d40d FOREIGN KEY (race_id) REFERENCES race (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE;

--
-- PostgreSQL database dump complete
--
