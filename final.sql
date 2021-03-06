--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: box2d; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE box2d;


--
-- Name: box2d_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box2d_in(cstring) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX2D_in';


ALTER FUNCTION public.box2d_in(cstring) OWNER TO postgres;

--
-- Name: box2d_out(box2d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box2d_out(box2d) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX2D_out';


ALTER FUNCTION public.box2d_out(box2d) OWNER TO postgres;

--
-- Name: box2d; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE box2d (
    INTERNALLENGTH = 65,
    INPUT = box2d_in,
    OUTPUT = box2d_out,
    ALIGNMENT = int4,
    STORAGE = plain
);


ALTER TYPE public.box2d OWNER TO postgres;

--
-- Name: box2df; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE box2df;


--
-- Name: box2df_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box2df_in(cstring) RETURNS box2df
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'box2df_in';


ALTER FUNCTION public.box2df_in(cstring) OWNER TO postgres;

--
-- Name: box2df_out(box2df); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box2df_out(box2df) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'box2df_out';


ALTER FUNCTION public.box2df_out(box2df) OWNER TO postgres;

--
-- Name: box2df; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE box2df (
    INTERNALLENGTH = 16,
    INPUT = box2df_in,
    OUTPUT = box2df_out,
    ALIGNMENT = double,
    STORAGE = plain
);


ALTER TYPE public.box2df OWNER TO postgres;

--
-- Name: box3d; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE box3d;


--
-- Name: box3d_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box3d_in(cstring) RETURNS box3d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_in';


ALTER FUNCTION public.box3d_in(cstring) OWNER TO postgres;

--
-- Name: box3d_out(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box3d_out(box3d) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_out';


ALTER FUNCTION public.box3d_out(box3d) OWNER TO postgres;

--
-- Name: box3d; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE box3d (
    INTERNALLENGTH = 52,
    INPUT = box3d_in,
    OUTPUT = box3d_out,
    ALIGNMENT = double,
    STORAGE = plain
);


ALTER TYPE public.box3d OWNER TO postgres;

--
-- Name: geography; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE geography;


--
-- Name: geography_analyze(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_analyze(internal) RETURNS boolean
    LANGUAGE c STRICT
    AS '$libdir/postgis-2.0', 'geography_analyze';


ALTER FUNCTION public.geography_analyze(internal) OWNER TO postgres;

--
-- Name: geography_in(cstring, oid, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_in(cstring, oid, integer) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_in';


ALTER FUNCTION public.geography_in(cstring, oid, integer) OWNER TO postgres;

--
-- Name: geography_out(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_out(geography) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_out';


ALTER FUNCTION public.geography_out(geography) OWNER TO postgres;

--
-- Name: geography_recv(internal, oid, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_recv(internal, oid, integer) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_recv';


ALTER FUNCTION public.geography_recv(internal, oid, integer) OWNER TO postgres;

--
-- Name: geography_send(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_send(geography) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_send';


ALTER FUNCTION public.geography_send(geography) OWNER TO postgres;

--
-- Name: geography_typmod_in(cstring[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_typmod_in(cstring[]) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_typmod_in';


ALTER FUNCTION public.geography_typmod_in(cstring[]) OWNER TO postgres;

--
-- Name: geography_typmod_out(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_typmod_out(integer) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'postgis_typmod_out';


ALTER FUNCTION public.geography_typmod_out(integer) OWNER TO postgres;

--
-- Name: geography; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE geography (
    INTERNALLENGTH = variable,
    INPUT = geography_in,
    OUTPUT = geography_out,
    RECEIVE = geography_recv,
    SEND = geography_send,
    TYPMOD_IN = geography_typmod_in,
    TYPMOD_OUT = geography_typmod_out,
    ANALYZE = geography_analyze,
    DELIMITER = ':',
    ALIGNMENT = double,
    STORAGE = main
);


ALTER TYPE public.geography OWNER TO postgres;

--
-- Name: geometry; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE geometry;


--
-- Name: geometry_analyze(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_analyze(internal) RETURNS boolean
    LANGUAGE c STRICT
    AS '$libdir/postgis-2.0', 'geometry_analyze_2d';


ALTER FUNCTION public.geometry_analyze(internal) OWNER TO postgres;

--
-- Name: geometry_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_in(cstring) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_in';


ALTER FUNCTION public.geometry_in(cstring) OWNER TO postgres;

--
-- Name: geometry_out(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_out(geometry) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_out';


ALTER FUNCTION public.geometry_out(geometry) OWNER TO postgres;

--
-- Name: geometry_recv(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_recv(internal) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_recv';


ALTER FUNCTION public.geometry_recv(internal) OWNER TO postgres;

--
-- Name: geometry_send(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_send(geometry) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_send';


ALTER FUNCTION public.geometry_send(geometry) OWNER TO postgres;

--
-- Name: geometry_typmod_in(cstring[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_typmod_in(cstring[]) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geometry_typmod_in';


ALTER FUNCTION public.geometry_typmod_in(cstring[]) OWNER TO postgres;

--
-- Name: geometry_typmod_out(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_typmod_out(integer) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'postgis_typmod_out';


ALTER FUNCTION public.geometry_typmod_out(integer) OWNER TO postgres;

--
-- Name: geometry; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE geometry (
    INTERNALLENGTH = variable,
    INPUT = geometry_in,
    OUTPUT = geometry_out,
    RECEIVE = geometry_recv,
    SEND = geometry_send,
    TYPMOD_IN = geometry_typmod_in,
    TYPMOD_OUT = geometry_typmod_out,
    ANALYZE = geometry_analyze,
    DELIMITER = ':',
    ALIGNMENT = double,
    STORAGE = main
);


ALTER TYPE public.geometry OWNER TO postgres;

--
-- Name: geometry_dump; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE geometry_dump AS (
	path integer[],
	geom geometry
);


ALTER TYPE public.geometry_dump OWNER TO postgres;

--
-- Name: gidx; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE gidx;


--
-- Name: gidx_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION gidx_in(cstring) RETURNS gidx
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gidx_in';


ALTER FUNCTION public.gidx_in(cstring) OWNER TO postgres;

--
-- Name: gidx_out(gidx); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION gidx_out(gidx) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gidx_out';


ALTER FUNCTION public.gidx_out(gidx) OWNER TO postgres;

--
-- Name: gidx; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE gidx (
    INTERNALLENGTH = variable,
    INPUT = gidx_in,
    OUTPUT = gidx_out,
    ALIGNMENT = double,
    STORAGE = plain
);


ALTER TYPE public.gidx OWNER TO postgres;

--
-- Name: pgis_abs; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE pgis_abs;


--
-- Name: pgis_abs_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_abs_in(cstring) RETURNS pgis_abs
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'pgis_abs_in';


ALTER FUNCTION public.pgis_abs_in(cstring) OWNER TO postgres;

--
-- Name: pgis_abs_out(pgis_abs); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_abs_out(pgis_abs) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'pgis_abs_out';


ALTER FUNCTION public.pgis_abs_out(pgis_abs) OWNER TO postgres;

--
-- Name: pgis_abs; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE pgis_abs (
    INTERNALLENGTH = 8,
    INPUT = pgis_abs_in,
    OUTPUT = pgis_abs_out,
    ALIGNMENT = double,
    STORAGE = plain
);


ALTER TYPE public.pgis_abs OWNER TO postgres;

--
-- Name: spheroid; Type: SHELL TYPE; Schema: public; Owner: postgres
--

CREATE TYPE spheroid;


--
-- Name: spheroid_in(cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION spheroid_in(cstring) RETURNS spheroid
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ellipsoid_in';


ALTER FUNCTION public.spheroid_in(cstring) OWNER TO postgres;

--
-- Name: spheroid_out(spheroid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION spheroid_out(spheroid) RETURNS cstring
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ellipsoid_out';


ALTER FUNCTION public.spheroid_out(spheroid) OWNER TO postgres;

--
-- Name: spheroid; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE spheroid (
    INTERNALLENGTH = 65,
    INPUT = spheroid_in,
    OUTPUT = spheroid_out,
    ALIGNMENT = double,
    STORAGE = plain
);


ALTER TYPE public.spheroid OWNER TO postgres;

--
-- Name: valid_detail; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE valid_detail AS (
	valid boolean,
	reason character varying,
	location geometry
);


ALTER TYPE public.valid_detail OWNER TO postgres;

--
-- Name: _st_3ddfullywithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_3ddfullywithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_dfullywithin3d';


ALTER FUNCTION public._st_3ddfullywithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: _st_3ddwithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_3ddwithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_dwithin3d';


ALTER FUNCTION public._st_3ddwithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: _st_asgeojson(integer, geometry, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_asgeojson(integer, geometry, integer, integer) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asGeoJson';


ALTER FUNCTION public._st_asgeojson(integer, geometry, integer, integer) OWNER TO postgres;

--
-- Name: _st_asgeojson(integer, geography, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_asgeojson(integer, geography, integer, integer) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_as_geojson';


ALTER FUNCTION public._st_asgeojson(integer, geography, integer, integer) OWNER TO postgres;

--
-- Name: _st_asgml(integer, geometry, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_asgml(integer, geometry, integer, integer, text) RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'LWGEOM_asGML';


ALTER FUNCTION public._st_asgml(integer, geometry, integer, integer, text) OWNER TO postgres;

--
-- Name: _st_asgml(integer, geography, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_asgml(integer, geography, integer, integer, text) RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'geography_as_gml';


ALTER FUNCTION public._st_asgml(integer, geography, integer, integer, text) OWNER TO postgres;

--
-- Name: _st_askml(integer, geometry, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_askml(integer, geometry, integer, text) RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'LWGEOM_asKML';


ALTER FUNCTION public._st_askml(integer, geometry, integer, text) OWNER TO postgres;

--
-- Name: _st_askml(integer, geography, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_askml(integer, geography, integer, text) RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'geography_as_kml';


ALTER FUNCTION public._st_askml(integer, geography, integer, text) OWNER TO postgres;

--
-- Name: _st_asx3d(integer, geometry, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_asx3d(integer, geometry, integer, integer, text) RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'LWGEOM_asX3D';


ALTER FUNCTION public._st_asx3d(integer, geometry, integer, integer, text) OWNER TO postgres;

--
-- Name: _st_bestsrid(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_bestsrid(geography) RETURNS integer
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_BestSRID($1,$1)$_$;


ALTER FUNCTION public._st_bestsrid(geography) OWNER TO postgres;

--
-- Name: _st_bestsrid(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_bestsrid(geography, geography) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_bestsrid';


ALTER FUNCTION public._st_bestsrid(geography, geography) OWNER TO postgres;

--
-- Name: _st_buffer(geometry, double precision, cstring); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_buffer(geometry, double precision, cstring) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'buffer';


ALTER FUNCTION public._st_buffer(geometry, double precision, cstring) OWNER TO postgres;

--
-- Name: _st_concavehull(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_concavehull(param_inputgeom geometry) RETURNS geometry
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
	DECLARE     
	vexhull GEOMETRY;
	var_resultgeom geometry;
	var_inputgeom geometry;
	vexring GEOMETRY;
	cavering GEOMETRY;
	cavept geometry[];
	seglength double precision;
	var_tempgeom geometry;
	scale_factor integer := 1;
	i integer;
	
	BEGIN

		-- First compute the ConvexHull of the geometry
		vexhull := ST_ConvexHull(param_inputgeom);
		var_inputgeom := param_inputgeom;
		--A point really has no concave hull
		IF ST_GeometryType(vexhull) = 'ST_Point' OR ST_GeometryType(vexHull) = 'ST_LineString' THEN
			RETURN vexhull;
		END IF;

		-- convert the hull perimeter to a linestring so we can manipulate individual points
		vexring := CASE WHEN ST_GeometryType(vexhull) = 'ST_LineString' THEN vexhull ELSE ST_ExteriorRing(vexhull) END;
		IF abs(ST_X(ST_PointN(vexring,1))) < 1 THEN --scale the geometry to prevent stupid precision errors - not sure it works so make low for now
			scale_factor := 100;
			vexring := ST_Scale(vexring, scale_factor,scale_factor);
			var_inputgeom := ST_Scale(var_inputgeom, scale_factor, scale_factor);
			--RAISE NOTICE 'Scaling';
		END IF;
		seglength := ST_Length(vexring)/least(ST_NPoints(vexring)*2,1000) ;

		vexring := ST_Segmentize(vexring, seglength);
		-- find the point on the original geom that is closest to each point of the convex hull and make a new linestring out of it.
		cavering := ST_Collect(
			ARRAY(

				SELECT 
					ST_ClosestPoint(var_inputgeom, pt ) As the_geom
					FROM (
						SELECT  ST_PointN(vexring, n ) As pt, n
							FROM 
							generate_series(1, ST_NPoints(vexring) ) As n
						) As pt
				
				)
			)
		; 
		

		var_resultgeom := ST_MakeLine(geom) 
			FROM ST_Dump(cavering) As foo;

		IF ST_IsSimple(var_resultgeom) THEN
			var_resultgeom := ST_MakePolygon(var_resultgeom);
			--RAISE NOTICE 'is Simple: %', var_resultgeom;
		ELSE 
			--RAISE NOTICE 'is not Simple: %', var_resultgeom;
			var_resultgeom := ST_ConvexHull(var_resultgeom);
		END IF;
		
		IF scale_factor > 1 THEN -- scale the result back
			var_resultgeom := ST_Scale(var_resultgeom, 1/scale_factor, 1/scale_factor);
		END IF;
		RETURN var_resultgeom;
	
	END;
$$;


ALTER FUNCTION public._st_concavehull(param_inputgeom geometry) OWNER TO postgres;

--
-- Name: _st_contains(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_contains(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'contains';


ALTER FUNCTION public._st_contains(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_containsproperly(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_containsproperly(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'containsproperly';


ALTER FUNCTION public._st_containsproperly(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_coveredby(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_coveredby(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'coveredby';


ALTER FUNCTION public._st_coveredby(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_covers(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_covers(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'covers';


ALTER FUNCTION public._st_covers(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_covers(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_covers(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_covers';


ALTER FUNCTION public._st_covers(geography, geography) OWNER TO postgres;

--
-- Name: _st_crosses(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_crosses(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'crosses';


ALTER FUNCTION public._st_crosses(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_dfullywithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_dfullywithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_dfullywithin';


ALTER FUNCTION public._st_dfullywithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: _st_distance(geography, geography, double precision, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_distance(geography, geography, double precision, boolean) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_distance';


ALTER FUNCTION public._st_distance(geography, geography, double precision, boolean) OWNER TO postgres;

--
-- Name: _st_dumppoints(geometry, integer[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_dumppoints(the_geom geometry, cur_path integer[]) RETURNS SETOF geometry_dump
    LANGUAGE plpgsql
    AS $$
DECLARE
  tmp geometry_dump;
  tmp2 geometry_dump;
  nb_points integer;
  nb_geom integer;
  i integer;
  j integer;
  g geometry;
  
BEGIN
  
  -- RAISE DEBUG '%,%', cur_path, ST_GeometryType(the_geom);

  -- Special case collections : iterate and return the DumpPoints of the geometries

  IF (ST_IsCollection(the_geom)) THEN
 
    i = 1;
    FOR tmp2 IN SELECT (ST_Dump(the_geom)).* LOOP

      FOR tmp IN SELECT * FROM _ST_DumpPoints(tmp2.geom, cur_path || tmp2.path) LOOP
	    RETURN NEXT tmp;
      END LOOP;
      i = i + 1;
      
    END LOOP;

    RETURN;
  END IF;
  

  -- Special case (POLYGON) : return the points of the rings of a polygon
  IF (ST_GeometryType(the_geom) = 'ST_Polygon') THEN

    FOR tmp IN SELECT * FROM _ST_DumpPoints(ST_ExteriorRing(the_geom), cur_path || ARRAY[1]) LOOP
      RETURN NEXT tmp;
    END LOOP;
    
    j := ST_NumInteriorRings(the_geom);
    FOR i IN 1..j LOOP
        FOR tmp IN SELECT * FROM _ST_DumpPoints(ST_InteriorRingN(the_geom, i), cur_path || ARRAY[i+1]) LOOP
          RETURN NEXT tmp;
        END LOOP;
    END LOOP;
    
    RETURN;
  END IF;

  -- Special case (TRIANGLE) : return the points of the external rings of a TRIANGLE
  IF (ST_GeometryType(the_geom) = 'ST_Triangle') THEN

    FOR tmp IN SELECT * FROM _ST_DumpPoints(ST_ExteriorRing(the_geom), cur_path || ARRAY[1]) LOOP
      RETURN NEXT tmp;
    END LOOP;
    
    RETURN;
  END IF;

    
  -- Special case (POINT) : return the point
  IF (ST_GeometryType(the_geom) = 'ST_Point') THEN

    tmp.path = cur_path || ARRAY[1];
    tmp.geom = the_geom;

    RETURN NEXT tmp;
    RETURN;

  END IF;


  -- Use ST_NumPoints rather than ST_NPoints to have a NULL value if the_geom isn't
  -- a LINESTRING, CIRCULARSTRING.
  SELECT ST_NumPoints(the_geom) INTO nb_points;

  -- This should never happen
  IF (nb_points IS NULL) THEN
    RAISE EXCEPTION 'Unexpected error while dumping geometry %', ST_AsText(the_geom);
  END IF;

  FOR i IN 1..nb_points LOOP
    tmp.path = cur_path || ARRAY[i];
    tmp.geom := ST_PointN(the_geom, i);
    RETURN NEXT tmp;
  END LOOP;
   
END
$$;


ALTER FUNCTION public._st_dumppoints(the_geom geometry, cur_path integer[]) OWNER TO postgres;

--
-- Name: _st_dwithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_dwithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_dwithin';


ALTER FUNCTION public._st_dwithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: _st_dwithin(geography, geography, double precision, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_dwithin(geography, geography, double precision, boolean) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_dwithin';


ALTER FUNCTION public._st_dwithin(geography, geography, double precision, boolean) OWNER TO postgres;

--
-- Name: _st_equals(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_equals(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_Equals';


ALTER FUNCTION public._st_equals(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_expand(geography, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_expand(geography, double precision) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_expand';


ALTER FUNCTION public._st_expand(geography, double precision) OWNER TO postgres;

--
-- Name: _st_geomfromgml(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_geomfromgml(text, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'geom_from_gml';


ALTER FUNCTION public._st_geomfromgml(text, integer) OWNER TO postgres;

--
-- Name: _st_intersects(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_intersects(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'intersects';


ALTER FUNCTION public._st_intersects(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_linecrossingdirection(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_linecrossingdirection(geom1 geometry, geom2 geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_LineCrossingDirection';


ALTER FUNCTION public._st_linecrossingdirection(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_longestline(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_longestline(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_longestline2d';


ALTER FUNCTION public._st_longestline(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_maxdistance(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_maxdistance(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_maxdistance2d_linestring';


ALTER FUNCTION public._st_maxdistance(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_orderingequals(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_orderingequals(geometrya geometry, geometryb geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_same';


ALTER FUNCTION public._st_orderingequals(geometrya geometry, geometryb geometry) OWNER TO postgres;

--
-- Name: _st_overlaps(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_overlaps(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'overlaps';


ALTER FUNCTION public._st_overlaps(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_pointoutside(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_pointoutside(geography) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_point_outside';


ALTER FUNCTION public._st_pointoutside(geography) OWNER TO postgres;

--
-- Name: _st_touches(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_touches(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'touches';


ALTER FUNCTION public._st_touches(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: _st_within(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION _st_within(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT _ST_Contains($2,$1)$_$;


ALTER FUNCTION public._st_within(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: addauth(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION addauth(text) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$ 
DECLARE
	lockid alias for $1;
	okay boolean;
	myrec record;
BEGIN
	-- check to see if table exists
	--  if not, CREATE TEMP TABLE mylock (transid xid, lockcode text)
	okay := 'f';
	FOR myrec IN SELECT * FROM pg_class WHERE relname = 'temp_lock_have_table' LOOP
		okay := 't';
	END LOOP; 
	IF (okay <> 't') THEN 
		CREATE TEMP TABLE temp_lock_have_table (transid xid, lockcode text);
			-- this will only work from pgsql7.4 up
			-- ON COMMIT DELETE ROWS;
	END IF;

	--  INSERT INTO mylock VALUES ( $1)
--	EXECUTE 'INSERT INTO temp_lock_have_table VALUES ( '||
--		quote_literal(getTransactionID()) || ',' ||
--		quote_literal(lockid) ||')';

	INSERT INTO temp_lock_have_table VALUES (getTransactionID(), lockid);

	RETURN true::boolean;
END;
$_$;


ALTER FUNCTION public.addauth(text) OWNER TO postgres;

--
-- Name: addgeometrycolumn(character varying, character varying, integer, character varying, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION addgeometrycolumn(table_name character varying, column_name character varying, new_srid integer, new_type character varying, new_dim integer, use_typmod boolean DEFAULT true) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $_$
DECLARE
	ret  text;
BEGIN
	SELECT AddGeometryColumn('','',$1,$2,$3,$4,$5, $6) into ret;
	RETURN ret;
END;
$_$;


ALTER FUNCTION public.addgeometrycolumn(table_name character varying, column_name character varying, new_srid integer, new_type character varying, new_dim integer, use_typmod boolean) OWNER TO postgres;

--
-- Name: addgeometrycolumn(character varying, character varying, character varying, integer, character varying, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION addgeometrycolumn(schema_name character varying, table_name character varying, column_name character varying, new_srid integer, new_type character varying, new_dim integer, use_typmod boolean DEFAULT true) RETURNS text
    LANGUAGE plpgsql STABLE STRICT
    AS $_$
DECLARE
	ret  text;
BEGIN
	SELECT AddGeometryColumn('',$1,$2,$3,$4,$5,$6,$7) into ret;
	RETURN ret;
END;
$_$;


ALTER FUNCTION public.addgeometrycolumn(schema_name character varying, table_name character varying, column_name character varying, new_srid integer, new_type character varying, new_dim integer, use_typmod boolean) OWNER TO postgres;

--
-- Name: addgeometrycolumn(character varying, character varying, character varying, character varying, integer, character varying, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION addgeometrycolumn(catalog_name character varying, schema_name character varying, table_name character varying, column_name character varying, new_srid_in integer, new_type character varying, new_dim integer, use_typmod boolean DEFAULT true) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $$
DECLARE
	rec RECORD;
	sr varchar;
	real_schema name;
	sql text;
	new_srid integer;

BEGIN

	-- Verify geometry type
	IF (postgis_type_name(new_type,new_dim) IS NULL )
	THEN
		RAISE EXCEPTION 'Invalid type name "%(%)" - valid ones are:
	POINT, MULTIPOINT,
	LINESTRING, MULTILINESTRING,
	POLYGON, MULTIPOLYGON,
	CIRCULARSTRING, COMPOUNDCURVE, MULTICURVE,
	CURVEPOLYGON, MULTISURFACE,
	GEOMETRY, GEOMETRYCOLLECTION,
	POINTM, MULTIPOINTM,
	LINESTRINGM, MULTILINESTRINGM,
	POLYGONM, MULTIPOLYGONM,
	CIRCULARSTRINGM, COMPOUNDCURVEM, MULTICURVEM
	CURVEPOLYGONM, MULTISURFACEM, TRIANGLE, TRIANGLEM,
	POLYHEDRALSURFACE, POLYHEDRALSURFACEM, TIN, TINM
	or GEOMETRYCOLLECTIONM', new_type, new_dim;
		RETURN 'fail';
	END IF;


	-- Verify dimension
	IF ( (new_dim >4) OR (new_dim <2) ) THEN
		RAISE EXCEPTION 'invalid dimension';
		RETURN 'fail';
	END IF;

	IF ( (new_type LIKE '%M') AND (new_dim!=3) ) THEN
		RAISE EXCEPTION 'TypeM needs 3 dimensions';
		RETURN 'fail';
	END IF;


	-- Verify SRID
	IF ( new_srid_in > 0 ) THEN
		IF new_srid_in > 998999 THEN
			RAISE EXCEPTION 'AddGeometryColumn() - SRID must be <= %', 998999;
		END IF;
		new_srid := new_srid_in;
		SELECT SRID INTO sr FROM spatial_ref_sys WHERE SRID = new_srid;
		IF NOT FOUND THEN
			RAISE EXCEPTION 'AddGeometryColumn() - invalid SRID';
			RETURN 'fail';
		END IF;
	ELSE
		new_srid := ST_SRID('POINT EMPTY'::geometry);
		IF ( new_srid_in != new_srid ) THEN
			RAISE NOTICE 'SRID value % converted to the officially unknown SRID value %', new_srid_in, new_srid;
		END IF;
	END IF;


	-- Verify schema
	IF ( schema_name IS NOT NULL AND schema_name != '' ) THEN
		sql := 'SELECT nspname FROM pg_namespace ' ||
			'WHERE text(nspname) = ' || quote_literal(schema_name) ||
			'LIMIT 1';
		RAISE DEBUG '%', sql;
		EXECUTE sql INTO real_schema;

		IF ( real_schema IS NULL ) THEN
			RAISE EXCEPTION 'Schema % is not a valid schemaname', quote_literal(schema_name);
			RETURN 'fail';
		END IF;
	END IF;

	IF ( real_schema IS NULL ) THEN
		RAISE DEBUG 'Detecting schema';
		sql := 'SELECT n.nspname AS schemaname ' ||
			'FROM pg_catalog.pg_class c ' ||
			  'JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace ' ||
			'WHERE c.relkind = ' || quote_literal('r') ||
			' AND n.nspname NOT IN (' || quote_literal('pg_catalog') || ', ' || quote_literal('pg_toast') || ')' ||
			' AND pg_catalog.pg_table_is_visible(c.oid)' ||
			' AND c.relname = ' || quote_literal(table_name);
		RAISE DEBUG '%', sql;
		EXECUTE sql INTO real_schema;

		IF ( real_schema IS NULL ) THEN
			RAISE EXCEPTION 'Table % does not occur in the search_path', quote_literal(table_name);
			RETURN 'fail';
		END IF;
	END IF;


	-- Add geometry column to table
	IF use_typmod THEN
	     sql := 'ALTER TABLE ' ||
            quote_ident(real_schema) || '.' || quote_ident(table_name)
            || ' ADD COLUMN ' || quote_ident(column_name) ||
            ' geometry(' || postgis_type_name(new_type, new_dim) || ', ' || new_srid::text || ')';
        RAISE DEBUG '%', sql;
	ELSE
        sql := 'ALTER TABLE ' ||
            quote_ident(real_schema) || '.' || quote_ident(table_name)
            || ' ADD COLUMN ' || quote_ident(column_name) ||
            ' geometry ';
        RAISE DEBUG '%', sql;
    END IF;
	EXECUTE sql;

	IF NOT use_typmod THEN
        -- Add table CHECKs
        sql := 'ALTER TABLE ' ||
            quote_ident(real_schema) || '.' || quote_ident(table_name)
            || ' ADD CONSTRAINT '
            || quote_ident('enforce_srid_' || column_name)
            || ' CHECK (st_srid(' || quote_ident(column_name) ||
            ') = ' || new_srid::text || ')' ;
        RAISE DEBUG '%', sql;
        EXECUTE sql;
    
        sql := 'ALTER TABLE ' ||
            quote_ident(real_schema) || '.' || quote_ident(table_name)
            || ' ADD CONSTRAINT '
            || quote_ident('enforce_dims_' || column_name)
            || ' CHECK (st_ndims(' || quote_ident(column_name) ||
            ') = ' || new_dim::text || ')' ;
        RAISE DEBUG '%', sql;
        EXECUTE sql;
    
        IF ( NOT (new_type = 'GEOMETRY')) THEN
            sql := 'ALTER TABLE ' ||
                quote_ident(real_schema) || '.' || quote_ident(table_name) || ' ADD CONSTRAINT ' ||
                quote_ident('enforce_geotype_' || column_name) ||
                ' CHECK (GeometryType(' ||
                quote_ident(column_name) || ')=' ||
                quote_literal(new_type) || ' OR (' ||
                quote_ident(column_name) || ') is null)';
            RAISE DEBUG '%', sql;
            EXECUTE sql;
        END IF;
    END IF;

	RETURN
		real_schema || '.' ||
		table_name || '.' || column_name ||
		' SRID:' || new_srid::text ||
		' TYPE:' || new_type ||
		' DIMS:' || new_dim::text || ' ';
END;
$$;


ALTER FUNCTION public.addgeometrycolumn(catalog_name character varying, schema_name character varying, table_name character varying, column_name character varying, new_srid_in integer, new_type character varying, new_dim integer, use_typmod boolean) OWNER TO postgres;

--
-- Name: box(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box(geometry) RETURNS box
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_BOX';


ALTER FUNCTION public.box(geometry) OWNER TO postgres;

--
-- Name: box(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box(box3d) RETURNS box
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_to_BOX';


ALTER FUNCTION public.box(box3d) OWNER TO postgres;

--
-- Name: box2d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box2d(geometry) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_BOX2D';


ALTER FUNCTION public.box2d(geometry) OWNER TO postgres;

--
-- Name: box2d(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box2d(box3d) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_to_BOX2D';


ALTER FUNCTION public.box2d(box3d) OWNER TO postgres;

--
-- Name: box3d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box3d(geometry) RETURNS box3d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_BOX3D';


ALTER FUNCTION public.box3d(geometry) OWNER TO postgres;

--
-- Name: box3d(box2d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box3d(box2d) RETURNS box3d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX2D_to_BOX3D';


ALTER FUNCTION public.box3d(box2d) OWNER TO postgres;

--
-- Name: box3dtobox(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION box3dtobox(box3d) RETURNS box
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT box($1)$_$;


ALTER FUNCTION public.box3dtobox(box3d) OWNER TO postgres;

--
-- Name: bytea(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION bytea(geometry) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_bytea';


ALTER FUNCTION public.bytea(geometry) OWNER TO postgres;

--
-- Name: bytea(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION bytea(geography) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_bytea';


ALTER FUNCTION public.bytea(geography) OWNER TO postgres;

--
-- Name: checkauth(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION checkauth(text, text) RETURNS integer
    LANGUAGE sql
    AS $_$ SELECT CheckAuth('', $1, $2) $_$;


ALTER FUNCTION public.checkauth(text, text) OWNER TO postgres;

--
-- Name: checkauth(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION checkauth(text, text, text) RETURNS integer
    LANGUAGE plpgsql
    AS $_$ 
DECLARE
	schema text;
BEGIN
	IF NOT LongTransactionsEnabled() THEN
		RAISE EXCEPTION 'Long transaction support disabled, use EnableLongTransaction() to enable.';
	END IF;

	if ( $1 != '' ) THEN
		schema = $1;
	ELSE
		SELECT current_schema() into schema;
	END IF;

	-- TODO: check for an already existing trigger ?

	EXECUTE 'CREATE TRIGGER check_auth BEFORE UPDATE OR DELETE ON ' 
		|| quote_ident(schema) || '.' || quote_ident($2)
		||' FOR EACH ROW EXECUTE PROCEDURE CheckAuthTrigger('
		|| quote_literal($3) || ')';

	RETURN 0;
END;
$_$;


ALTER FUNCTION public.checkauth(text, text, text) OWNER TO postgres;

--
-- Name: checkauthtrigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION checkauthtrigger() RETURNS trigger
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'check_authorization';


ALTER FUNCTION public.checkauthtrigger() OWNER TO postgres;

--
-- Name: disablelongtransactions(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION disablelongtransactions() RETURNS text
    LANGUAGE plpgsql
    AS $$ 
DECLARE
	rec RECORD;

BEGIN

	--
	-- Drop all triggers applied by CheckAuth()
	--
	FOR rec IN
		SELECT c.relname, t.tgname, t.tgargs FROM pg_trigger t, pg_class c, pg_proc p
		WHERE p.proname = 'checkauthtrigger' and t.tgfoid = p.oid and t.tgrelid = c.oid
	LOOP
		EXECUTE 'DROP TRIGGER ' || quote_ident(rec.tgname) ||
			' ON ' || quote_ident(rec.relname);
	END LOOP;

	--
	-- Drop the authorization_table table
	--
	FOR rec IN SELECT * FROM pg_class WHERE relname = 'authorization_table' LOOP
		DROP TABLE authorization_table;
	END LOOP;

	--
	-- Drop the authorized_tables view
	--
	FOR rec IN SELECT * FROM pg_class WHERE relname = 'authorized_tables' LOOP
		DROP VIEW authorized_tables;
	END LOOP;

	RETURN 'Long transactions support disabled';
END;
$$;


ALTER FUNCTION public.disablelongtransactions() OWNER TO postgres;

--
-- Name: dropgeometrycolumn(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION dropgeometrycolumn(table_name character varying, column_name character varying) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $_$
DECLARE
	ret text;
BEGIN
	SELECT DropGeometryColumn('','',$1,$2) into ret;
	RETURN ret;
END;
$_$;


ALTER FUNCTION public.dropgeometrycolumn(table_name character varying, column_name character varying) OWNER TO postgres;

--
-- Name: dropgeometrycolumn(character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION dropgeometrycolumn(schema_name character varying, table_name character varying, column_name character varying) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $_$
DECLARE
	ret text;
BEGIN
	SELECT DropGeometryColumn('',$1,$2,$3) into ret;
	RETURN ret;
END;
$_$;


ALTER FUNCTION public.dropgeometrycolumn(schema_name character varying, table_name character varying, column_name character varying) OWNER TO postgres;

--
-- Name: dropgeometrycolumn(character varying, character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION dropgeometrycolumn(catalog_name character varying, schema_name character varying, table_name character varying, column_name character varying) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $$
DECLARE
	myrec RECORD;
	okay boolean;
	real_schema name;

BEGIN


	-- Find, check or fix schema_name
	IF ( schema_name != '' ) THEN
		okay = false;

		FOR myrec IN SELECT nspname FROM pg_namespace WHERE text(nspname) = schema_name LOOP
			okay := true;
		END LOOP;

		IF ( okay <>  true ) THEN
			RAISE NOTICE 'Invalid schema name - using current_schema()';
			SELECT current_schema() into real_schema;
		ELSE
			real_schema = schema_name;
		END IF;
	ELSE
		SELECT current_schema() into real_schema;
	END IF;

	-- Find out if the column is in the geometry_columns table
	okay = false;
	FOR myrec IN SELECT * from geometry_columns where f_table_schema = text(real_schema) and f_table_name = table_name and f_geometry_column = column_name LOOP
		okay := true;
	END LOOP;
	IF (okay <> true) THEN
		RAISE EXCEPTION 'column not found in geometry_columns table';
		RETURN false;
	END IF;

	-- Remove table column
	EXECUTE 'ALTER TABLE ' || quote_ident(real_schema) || '.' ||
		quote_ident(table_name) || ' DROP COLUMN ' ||
		quote_ident(column_name);

	RETURN real_schema || '.' || table_name || '.' || column_name ||' effectively removed.';

END;
$$;


ALTER FUNCTION public.dropgeometrycolumn(catalog_name character varying, schema_name character varying, table_name character varying, column_name character varying) OWNER TO postgres;

--
-- Name: dropgeometrytable(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION dropgeometrytable(table_name character varying) RETURNS text
    LANGUAGE sql STRICT
    AS $_$ SELECT DropGeometryTable('','',$1) $_$;


ALTER FUNCTION public.dropgeometrytable(table_name character varying) OWNER TO postgres;

--
-- Name: dropgeometrytable(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION dropgeometrytable(schema_name character varying, table_name character varying) RETURNS text
    LANGUAGE sql STRICT
    AS $_$ SELECT DropGeometryTable('',$1,$2) $_$;


ALTER FUNCTION public.dropgeometrytable(schema_name character varying, table_name character varying) OWNER TO postgres;

--
-- Name: dropgeometrytable(character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION dropgeometrytable(catalog_name character varying, schema_name character varying, table_name character varying) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $$
DECLARE
	real_schema name;

BEGIN

	IF ( schema_name = '' ) THEN
		SELECT current_schema() into real_schema;
	ELSE
		real_schema = schema_name;
	END IF;

	-- TODO: Should we warn if table doesn't exist probably instead just saying dropped
	-- Remove table
	EXECUTE 'DROP TABLE IF EXISTS '
		|| quote_ident(real_schema) || '.' ||
		quote_ident(table_name) || ' RESTRICT';

	RETURN
		real_schema || '.' ||
		table_name ||' dropped.';

END;
$$;


ALTER FUNCTION public.dropgeometrytable(catalog_name character varying, schema_name character varying, table_name character varying) OWNER TO postgres;

--
-- Name: enablelongtransactions(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION enablelongtransactions() RETURNS text
    LANGUAGE plpgsql
    AS $$ 
DECLARE
	"query" text;
	exists bool;
	rec RECORD;

BEGIN

	exists = 'f';
	FOR rec IN SELECT * FROM pg_class WHERE relname = 'authorization_table'
	LOOP
		exists = 't';
	END LOOP;

	IF NOT exists
	THEN
		"query" = 'CREATE TABLE authorization_table (
			toid oid, -- table oid
			rid text, -- row id
			expires timestamp,
			authid text
		)';
		EXECUTE "query";
	END IF;

	exists = 'f';
	FOR rec IN SELECT * FROM pg_class WHERE relname = 'authorized_tables'
	LOOP
		exists = 't';
	END LOOP;

	IF NOT exists THEN
		"query" = 'CREATE VIEW authorized_tables AS ' ||
			'SELECT ' ||
			'n.nspname as schema, ' ||
			'c.relname as table, trim(' ||
			quote_literal(chr(92) || '000') ||
			' from t.tgargs) as id_column ' ||
			'FROM pg_trigger t, pg_class c, pg_proc p ' ||
			', pg_namespace n ' ||
			'WHERE p.proname = ' || quote_literal('checkauthtrigger') ||
			' AND c.relnamespace = n.oid' ||
			' AND t.tgfoid = p.oid and t.tgrelid = c.oid';
		EXECUTE "query";
	END IF;

	RETURN 'Long transactions support enabled';
END;
$$;


ALTER FUNCTION public.enablelongtransactions() OWNER TO postgres;

--
-- Name: equals(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION equals(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_Equals';


ALTER FUNCTION public.equals(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: find_srid(character varying, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION find_srid(character varying, character varying, character varying) RETURNS integer
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
	schem text;
	tabl text;
	sr int4;
BEGIN
	IF $1 IS NULL THEN
	  RAISE EXCEPTION 'find_srid() - schema is NULL!';
	END IF;
	IF $2 IS NULL THEN
	  RAISE EXCEPTION 'find_srid() - table name is NULL!';
	END IF;
	IF $3 IS NULL THEN
	  RAISE EXCEPTION 'find_srid() - column name is NULL!';
	END IF;
	schem = $1;
	tabl = $2;
-- if the table contains a . and the schema is empty
-- split the table into a schema and a table
-- otherwise drop through to default behavior
	IF ( schem = '' and tabl LIKE '%.%' ) THEN
	 schem = substr(tabl,1,strpos(tabl,'.')-1);
	 tabl = substr(tabl,length(schem)+2);
	ELSE
	 schem = schem || '%';
	END IF;

	select SRID into sr from geometry_columns where f_table_schema like schem and f_table_name = tabl and f_geometry_column = $3;
	IF NOT FOUND THEN
	   RAISE EXCEPTION 'find_srid() - couldnt find the corresponding SRID - is the geometry registered in the GEOMETRY_COLUMNS table?  Is there an uppercase/lowercase missmatch?';
	END IF;
	return sr;
END;
$_$;


ALTER FUNCTION public.find_srid(character varying, character varying, character varying) OWNER TO postgres;

--
-- Name: geography(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography(bytea) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_bytea';


ALTER FUNCTION public.geography(bytea) OWNER TO postgres;

--
-- Name: geography(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography(geometry) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_from_geometry';


ALTER FUNCTION public.geography(geometry) OWNER TO postgres;

--
-- Name: geography(geography, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography(geography, integer, boolean) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_enforce_typmod';


ALTER FUNCTION public.geography(geography, integer, boolean) OWNER TO postgres;

--
-- Name: geography_cmp(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_cmp(geography, geography) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_cmp';


ALTER FUNCTION public.geography_cmp(geography, geography) OWNER TO postgres;

--
-- Name: geography_eq(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_eq(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_eq';


ALTER FUNCTION public.geography_eq(geography, geography) OWNER TO postgres;

--
-- Name: geography_ge(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_ge(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_ge';


ALTER FUNCTION public.geography_ge(geography, geography) OWNER TO postgres;

--
-- Name: geography_gist_compress(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_compress(internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_compress';


ALTER FUNCTION public.geography_gist_compress(internal) OWNER TO postgres;

--
-- Name: geography_gist_consistent(internal, geography, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_consistent(internal, geography, integer) RETURNS boolean
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_consistent';


ALTER FUNCTION public.geography_gist_consistent(internal, geography, integer) OWNER TO postgres;

--
-- Name: geography_gist_decompress(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_decompress(internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_decompress';


ALTER FUNCTION public.geography_gist_decompress(internal) OWNER TO postgres;

--
-- Name: geography_gist_join_selectivity(internal, oid, internal, smallint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_join_selectivity(internal, oid, internal, smallint) RETURNS double precision
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'geography_gist_selectivity';


ALTER FUNCTION public.geography_gist_join_selectivity(internal, oid, internal, smallint) OWNER TO postgres;

--
-- Name: geography_gist_penalty(internal, internal, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_penalty(internal, internal, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_penalty';


ALTER FUNCTION public.geography_gist_penalty(internal, internal, internal) OWNER TO postgres;

--
-- Name: geography_gist_picksplit(internal, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_picksplit(internal, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_picksplit';


ALTER FUNCTION public.geography_gist_picksplit(internal, internal) OWNER TO postgres;

--
-- Name: geography_gist_same(box2d, box2d, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_same(box2d, box2d, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_same';


ALTER FUNCTION public.geography_gist_same(box2d, box2d, internal) OWNER TO postgres;

--
-- Name: geography_gist_selectivity(internal, oid, internal, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_selectivity(internal, oid, internal, integer) RETURNS double precision
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'geography_gist_selectivity';


ALTER FUNCTION public.geography_gist_selectivity(internal, oid, internal, integer) OWNER TO postgres;

--
-- Name: geography_gist_union(bytea, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gist_union(bytea, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_union';


ALTER FUNCTION public.geography_gist_union(bytea, internal) OWNER TO postgres;

--
-- Name: geography_gt(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_gt(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_gt';


ALTER FUNCTION public.geography_gt(geography, geography) OWNER TO postgres;

--
-- Name: geography_le(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_le(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_le';


ALTER FUNCTION public.geography_le(geography, geography) OWNER TO postgres;

--
-- Name: geography_lt(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_lt(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_lt';


ALTER FUNCTION public.geography_lt(geography, geography) OWNER TO postgres;

--
-- Name: geography_overlaps(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geography_overlaps(geography, geography) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overlaps';


ALTER FUNCTION public.geography_overlaps(geography, geography) OWNER TO postgres;

--
-- Name: geometry(box2d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry(box2d) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX2D_to_LWGEOM';


ALTER FUNCTION public.geometry(box2d) OWNER TO postgres;

--
-- Name: geometry(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry(box3d) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_to_LWGEOM';


ALTER FUNCTION public.geometry(box3d) OWNER TO postgres;

--
-- Name: geometry(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'parse_WKT_lwgeom';


ALTER FUNCTION public.geometry(text) OWNER TO postgres;

--
-- Name: geometry(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry(bytea) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_bytea';


ALTER FUNCTION public.geometry(bytea) OWNER TO postgres;

--
-- Name: geometry(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry(geography) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geometry_from_geography';


ALTER FUNCTION public.geometry(geography) OWNER TO postgres;

--
-- Name: geometry(geometry, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry(geometry, integer, boolean) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geometry_enforce_typmod';


ALTER FUNCTION public.geometry(geometry, integer, boolean) OWNER TO postgres;

--
-- Name: geometry_above(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_above(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_above_2d';


ALTER FUNCTION public.geometry_above(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_below(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_below(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_below_2d';


ALTER FUNCTION public.geometry_below(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_cmp(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_cmp(geom1 geometry, geom2 geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'lwgeom_cmp';


ALTER FUNCTION public.geometry_cmp(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_contains(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_contains(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_contains_2d';


ALTER FUNCTION public.geometry_contains(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_distance_box(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_distance_box(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_distance_box_2d';


ALTER FUNCTION public.geometry_distance_box(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_distance_centroid(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_distance_centroid(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_distance_centroid_2d';


ALTER FUNCTION public.geometry_distance_centroid(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_eq(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_eq(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'lwgeom_eq';


ALTER FUNCTION public.geometry_eq(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_ge(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_ge(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'lwgeom_ge';


ALTER FUNCTION public.geometry_ge(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_gist_compress_2d(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_compress_2d(internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_compress_2d';


ALTER FUNCTION public.geometry_gist_compress_2d(internal) OWNER TO postgres;

--
-- Name: geometry_gist_compress_nd(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_compress_nd(internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_compress';


ALTER FUNCTION public.geometry_gist_compress_nd(internal) OWNER TO postgres;

--
-- Name: geometry_gist_consistent_2d(internal, geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_consistent_2d(internal, geometry, integer) RETURNS boolean
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_consistent_2d';


ALTER FUNCTION public.geometry_gist_consistent_2d(internal, geometry, integer) OWNER TO postgres;

--
-- Name: geometry_gist_consistent_nd(internal, geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_consistent_nd(internal, geometry, integer) RETURNS boolean
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_consistent';


ALTER FUNCTION public.geometry_gist_consistent_nd(internal, geometry, integer) OWNER TO postgres;

--
-- Name: geometry_gist_decompress_2d(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_decompress_2d(internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_decompress_2d';


ALTER FUNCTION public.geometry_gist_decompress_2d(internal) OWNER TO postgres;

--
-- Name: geometry_gist_decompress_nd(internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_decompress_nd(internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_decompress';


ALTER FUNCTION public.geometry_gist_decompress_nd(internal) OWNER TO postgres;

--
-- Name: geometry_gist_distance_2d(internal, geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_distance_2d(internal, geometry, integer) RETURNS double precision
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_distance_2d';


ALTER FUNCTION public.geometry_gist_distance_2d(internal, geometry, integer) OWNER TO postgres;

--
-- Name: geometry_gist_joinsel_2d(internal, oid, internal, smallint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_joinsel_2d(internal, oid, internal, smallint) RETURNS double precision
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'geometry_gist_joinsel_2d';


ALTER FUNCTION public.geometry_gist_joinsel_2d(internal, oid, internal, smallint) OWNER TO postgres;

--
-- Name: geometry_gist_penalty_2d(internal, internal, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_penalty_2d(internal, internal, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_penalty_2d';


ALTER FUNCTION public.geometry_gist_penalty_2d(internal, internal, internal) OWNER TO postgres;

--
-- Name: geometry_gist_penalty_nd(internal, internal, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_penalty_nd(internal, internal, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_penalty';


ALTER FUNCTION public.geometry_gist_penalty_nd(internal, internal, internal) OWNER TO postgres;

--
-- Name: geometry_gist_picksplit_2d(internal, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_picksplit_2d(internal, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_picksplit_2d';


ALTER FUNCTION public.geometry_gist_picksplit_2d(internal, internal) OWNER TO postgres;

--
-- Name: geometry_gist_picksplit_nd(internal, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_picksplit_nd(internal, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_picksplit';


ALTER FUNCTION public.geometry_gist_picksplit_nd(internal, internal) OWNER TO postgres;

--
-- Name: geometry_gist_same_2d(geometry, geometry, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_same_2d(geom1 geometry, geom2 geometry, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_same_2d';


ALTER FUNCTION public.geometry_gist_same_2d(geom1 geometry, geom2 geometry, internal) OWNER TO postgres;

--
-- Name: geometry_gist_same_nd(geometry, geometry, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_same_nd(geometry, geometry, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_same';


ALTER FUNCTION public.geometry_gist_same_nd(geometry, geometry, internal) OWNER TO postgres;

--
-- Name: geometry_gist_sel_2d(internal, oid, internal, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_sel_2d(internal, oid, internal, integer) RETURNS double precision
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'geometry_gist_sel_2d';


ALTER FUNCTION public.geometry_gist_sel_2d(internal, oid, internal, integer) OWNER TO postgres;

--
-- Name: geometry_gist_union_2d(bytea, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_union_2d(bytea, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_union_2d';


ALTER FUNCTION public.geometry_gist_union_2d(bytea, internal) OWNER TO postgres;

--
-- Name: geometry_gist_union_nd(bytea, internal); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gist_union_nd(bytea, internal) RETURNS internal
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'gserialized_gist_union';


ALTER FUNCTION public.geometry_gist_union_nd(bytea, internal) OWNER TO postgres;

--
-- Name: geometry_gt(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_gt(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'lwgeom_gt';


ALTER FUNCTION public.geometry_gt(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_le(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_le(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'lwgeom_le';


ALTER FUNCTION public.geometry_le(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_left(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_left(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_left_2d';


ALTER FUNCTION public.geometry_left(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_lt(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_lt(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'lwgeom_lt';


ALTER FUNCTION public.geometry_lt(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_overabove(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_overabove(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overabove_2d';


ALTER FUNCTION public.geometry_overabove(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_overbelow(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_overbelow(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overbelow_2d';


ALTER FUNCTION public.geometry_overbelow(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_overlaps(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_overlaps(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overlaps_2d';


ALTER FUNCTION public.geometry_overlaps(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_overlaps_nd(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_overlaps_nd(geometry, geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overlaps';


ALTER FUNCTION public.geometry_overlaps_nd(geometry, geometry) OWNER TO postgres;

--
-- Name: geometry_overleft(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_overleft(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overleft_2d';


ALTER FUNCTION public.geometry_overleft(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_overright(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_overright(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_overright_2d';


ALTER FUNCTION public.geometry_overright(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_right(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_right(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_right_2d';


ALTER FUNCTION public.geometry_right(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_same(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_same(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_same_2d';


ALTER FUNCTION public.geometry_same(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometry_within(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometry_within(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'gserialized_within_2d';


ALTER FUNCTION public.geometry_within(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: geometrytype(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometrytype(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_getTYPE';


ALTER FUNCTION public.geometrytype(geometry) OWNER TO postgres;

--
-- Name: geometrytype(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geometrytype(geography) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_getTYPE';


ALTER FUNCTION public.geometrytype(geography) OWNER TO postgres;

--
-- Name: geomfromewkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geomfromewkb(bytea) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOMFromWKB';


ALTER FUNCTION public.geomfromewkb(bytea) OWNER TO postgres;

--
-- Name: geomfromewkt(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION geomfromewkt(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'parse_WKT_lwgeom';


ALTER FUNCTION public.geomfromewkt(text) OWNER TO postgres;

--
-- Name: get_proj4_from_srid(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION get_proj4_from_srid(integer) RETURNS text
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
BEGIN
	RETURN proj4text::text FROM spatial_ref_sys WHERE srid= $1;
END;
$_$;


ALTER FUNCTION public.get_proj4_from_srid(integer) OWNER TO postgres;

--
-- Name: gettransactionid(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION gettransactionid() RETURNS xid
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'getTransactionID';


ALTER FUNCTION public.gettransactionid() OWNER TO postgres;

--
-- Name: lockrow(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION lockrow(text, text, text) RETURNS integer
    LANGUAGE sql STRICT
    AS $_$ SELECT LockRow(current_schema(), $1, $2, $3, now()::timestamp+'1:00'); $_$;


ALTER FUNCTION public.lockrow(text, text, text) OWNER TO postgres;

--
-- Name: lockrow(text, text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION lockrow(text, text, text, text) RETURNS integer
    LANGUAGE sql STRICT
    AS $_$ SELECT LockRow($1, $2, $3, $4, now()::timestamp+'1:00'); $_$;


ALTER FUNCTION public.lockrow(text, text, text, text) OWNER TO postgres;

--
-- Name: lockrow(text, text, text, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION lockrow(text, text, text, timestamp without time zone) RETURNS integer
    LANGUAGE sql STRICT
    AS $_$ SELECT LockRow(current_schema(), $1, $2, $3, $4); $_$;


ALTER FUNCTION public.lockrow(text, text, text, timestamp without time zone) OWNER TO postgres;

--
-- Name: lockrow(text, text, text, text, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION lockrow(text, text, text, text, timestamp without time zone) RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $_$ 
DECLARE
	myschema alias for $1;
	mytable alias for $2;
	myrid   alias for $3;
	authid alias for $4;
	expires alias for $5;
	ret int;
	mytoid oid;
	myrec RECORD;
	
BEGIN

	IF NOT LongTransactionsEnabled() THEN
		RAISE EXCEPTION 'Long transaction support disabled, use EnableLongTransaction() to enable.';
	END IF;

	EXECUTE 'DELETE FROM authorization_table WHERE expires < now()'; 

	SELECT c.oid INTO mytoid FROM pg_class c, pg_namespace n
		WHERE c.relname = mytable
		AND c.relnamespace = n.oid
		AND n.nspname = myschema;

	-- RAISE NOTICE 'toid: %', mytoid;

	FOR myrec IN SELECT * FROM authorization_table WHERE 
		toid = mytoid AND rid = myrid
	LOOP
		IF myrec.authid != authid THEN
			RETURN 0;
		ELSE
			RETURN 1;
		END IF;
	END LOOP;

	EXECUTE 'INSERT INTO authorization_table VALUES ('||
		quote_literal(mytoid::text)||','||quote_literal(myrid)||
		','||quote_literal(expires::text)||
		','||quote_literal(authid) ||')';

	GET DIAGNOSTICS ret = ROW_COUNT;

	RETURN ret;
END;
$_$;


ALTER FUNCTION public.lockrow(text, text, text, text, timestamp without time zone) OWNER TO postgres;

--
-- Name: longtransactionsenabled(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION longtransactionsenabled() RETURNS boolean
    LANGUAGE plpgsql
    AS $$ 
DECLARE
	rec RECORD;
BEGIN
	FOR rec IN SELECT oid FROM pg_class WHERE relname = 'authorized_tables'
	LOOP
		return 't';
	END LOOP;
	return 'f';
END;
$$;


ALTER FUNCTION public.longtransactionsenabled() OWNER TO postgres;

--
-- Name: pgis_geometry_accum_finalfn(pgis_abs); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_geometry_accum_finalfn(pgis_abs) RETURNS geometry[]
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'pgis_geometry_accum_finalfn';


ALTER FUNCTION public.pgis_geometry_accum_finalfn(pgis_abs) OWNER TO postgres;

--
-- Name: pgis_geometry_accum_transfn(pgis_abs, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_geometry_accum_transfn(pgis_abs, geometry) RETURNS pgis_abs
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'pgis_geometry_accum_transfn';


ALTER FUNCTION public.pgis_geometry_accum_transfn(pgis_abs, geometry) OWNER TO postgres;

--
-- Name: pgis_geometry_collect_finalfn(pgis_abs); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_geometry_collect_finalfn(pgis_abs) RETURNS geometry
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'pgis_geometry_collect_finalfn';


ALTER FUNCTION public.pgis_geometry_collect_finalfn(pgis_abs) OWNER TO postgres;

--
-- Name: pgis_geometry_makeline_finalfn(pgis_abs); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_geometry_makeline_finalfn(pgis_abs) RETURNS geometry
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'pgis_geometry_makeline_finalfn';


ALTER FUNCTION public.pgis_geometry_makeline_finalfn(pgis_abs) OWNER TO postgres;

--
-- Name: pgis_geometry_polygonize_finalfn(pgis_abs); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_geometry_polygonize_finalfn(pgis_abs) RETURNS geometry
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'pgis_geometry_polygonize_finalfn';


ALTER FUNCTION public.pgis_geometry_polygonize_finalfn(pgis_abs) OWNER TO postgres;

--
-- Name: pgis_geometry_union_finalfn(pgis_abs); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION pgis_geometry_union_finalfn(pgis_abs) RETURNS geometry
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'pgis_geometry_union_finalfn';


ALTER FUNCTION public.pgis_geometry_union_finalfn(pgis_abs) OWNER TO postgres;

--
-- Name: populate_geometry_columns(boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION populate_geometry_columns(use_typmod boolean DEFAULT true) RETURNS text
    LANGUAGE plpgsql
    AS $$
DECLARE
	inserted    integer;
	oldcount    integer;
	probed      integer;
	stale       integer;
	gcs         RECORD;
	gc          RECORD;
	gsrid       integer;
	gndims      integer;
	gtype       text;
	query       text;
	gc_is_valid boolean;

BEGIN
	SELECT count(*) INTO oldcount FROM geometry_columns;
	inserted := 0;

	-- Count the number of geometry columns in all tables and views
	SELECT count(DISTINCT c.oid) INTO probed
	FROM pg_class c,
		 pg_attribute a,
		 pg_type t,
		 pg_namespace n
	WHERE (c.relkind = 'r' OR c.relkind = 'v')
		AND t.typname = 'geometry'
		AND a.attisdropped = false
		AND a.atttypid = t.oid
		AND a.attrelid = c.oid
		AND c.relnamespace = n.oid
		AND n.nspname NOT ILIKE 'pg_temp%' AND c.relname != 'raster_columns' ;

	-- Iterate through all non-dropped geometry columns
	RAISE DEBUG 'Processing Tables.....';

	FOR gcs IN
	SELECT DISTINCT ON (c.oid) c.oid, n.nspname, c.relname
		FROM pg_class c,
			 pg_attribute a,
			 pg_type t,
			 pg_namespace n
		WHERE c.relkind = 'r'
		AND t.typname = 'geometry'
		AND a.attisdropped = false
		AND a.atttypid = t.oid
		AND a.attrelid = c.oid
		AND c.relnamespace = n.oid
		AND n.nspname NOT ILIKE 'pg_temp%' AND c.relname != 'raster_columns' 
	LOOP

		inserted := inserted + populate_geometry_columns(gcs.oid, use_typmod);
	END LOOP;

	IF oldcount > inserted THEN
	    stale = oldcount-inserted;
	ELSE
	    stale = 0;
	END IF;

	RETURN 'probed:' ||probed|| ' inserted:'||inserted;
END

$$;


ALTER FUNCTION public.populate_geometry_columns(use_typmod boolean) OWNER TO postgres;

--
-- Name: populate_geometry_columns(oid, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION populate_geometry_columns(tbl_oid oid, use_typmod boolean DEFAULT true) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
	gcs         RECORD;
	gc          RECORD;
	gc_old      RECORD;
	gsrid       integer;
	gndims      integer;
	gtype       text;
	query       text;
	gc_is_valid boolean;
	inserted    integer;
	constraint_successful boolean := false;

BEGIN
	inserted := 0;

	-- Iterate through all geometry columns in this table
	FOR gcs IN
	SELECT n.nspname, c.relname, a.attname
		FROM pg_class c,
			 pg_attribute a,
			 pg_type t,
			 pg_namespace n
		WHERE c.relkind = 'r'
		AND t.typname = 'geometry'
		AND a.attisdropped = false
		AND a.atttypid = t.oid
		AND a.attrelid = c.oid
		AND c.relnamespace = n.oid
		AND n.nspname NOT ILIKE 'pg_temp%'
		AND c.oid = tbl_oid
	LOOP

        RAISE DEBUG 'Processing table %.%.%', gcs.nspname, gcs.relname, gcs.attname;
    
        gc_is_valid := true;
        -- Find the srid, coord_dimension, and type of current geometry
        -- in geometry_columns -- which is now a view
        
        SELECT type, srid, coord_dimension INTO gc_old 
            FROM geometry_columns 
            WHERE f_table_schema = gcs.nspname AND f_table_name = gcs.relname AND f_geometry_column = gcs.attname; 
            
        IF upper(gc_old.type) = 'GEOMETRY' THEN
        -- This is an unconstrained geometry we need to do something
        -- We need to figure out what to set the type by inspecting the data
            EXECUTE 'SELECT st_srid(' || quote_ident(gcs.attname) || ') As srid, GeometryType(' || quote_ident(gcs.attname) || ') As type, ST_NDims(' || quote_ident(gcs.attname) || ') As dims ' ||
                     ' FROM ONLY ' || quote_ident(gcs.nspname) || '.' || quote_ident(gcs.relname) || 
                     ' WHERE ' || quote_ident(gcs.attname) || ' IS NOT NULL LIMIT 1;'
                INTO gc;
            IF gc IS NULL THEN -- there is no data so we can not determine geometry type
            	RAISE WARNING 'No data in table %.%, so no information to determine geometry type and srid', gcs.nspname, gcs.relname;
            	RETURN 0;
            END IF;
            gsrid := gc.srid; gtype := gc.type; gndims := gc.dims;
            	
            IF use_typmod THEN
                BEGIN
                    EXECUTE 'ALTER TABLE ' || quote_ident(gcs.nspname) || '.' || quote_ident(gcs.relname) || ' ALTER COLUMN ' || quote_ident(gcs.attname) || 
                        ' TYPE geometry(' || postgis_type_name(gtype, gndims, true) || ', ' || gsrid::text  || ') ';
                    inserted := inserted + 1;
                EXCEPTION
                        WHEN invalid_parameter_value THEN
                        RAISE WARNING 'Could not convert ''%'' in ''%.%'' to use typmod with srid %, type: % ', quote_ident(gcs.attname), quote_ident(gcs.nspname), quote_ident(gcs.relname), gsrid, postgis_type_name(gtype, gndims, true);
                            gc_is_valid := false;
                END;
                
            ELSE
                -- Try to apply srid check to column
            	constraint_successful = false;
                IF (gsrid > 0 AND postgis_constraint_srid(gcs.nspname, gcs.relname,gcs.attname) IS NULL ) THEN
                    BEGIN
                        EXECUTE 'ALTER TABLE ONLY ' || quote_ident(gcs.nspname) || '.' || quote_ident(gcs.relname) || 
                                 ' ADD CONSTRAINT ' || quote_ident('enforce_srid_' || gcs.attname) || 
                                 ' CHECK (st_srid(' || quote_ident(gcs.attname) || ') = ' || gsrid || ')';
                        constraint_successful := true;
                    EXCEPTION
                        WHEN check_violation THEN
                            RAISE WARNING 'Not inserting ''%'' in ''%.%'' into geometry_columns: could not apply constraint CHECK (st_srid(%) = %)', quote_ident(gcs.attname), quote_ident(gcs.nspname), quote_ident(gcs.relname), quote_ident(gcs.attname), gsrid;
                            gc_is_valid := false;
                    END;
                END IF;
                
                -- Try to apply ndims check to column
                IF (gndims IS NOT NULL AND postgis_constraint_dims(gcs.nspname, gcs.relname,gcs.attname) IS NULL ) THEN
                    BEGIN
                        EXECUTE 'ALTER TABLE ONLY ' || quote_ident(gcs.nspname) || '.' || quote_ident(gcs.relname) || '
                                 ADD CONSTRAINT ' || quote_ident('enforce_dims_' || gcs.attname) || '
                                 CHECK (st_ndims(' || quote_ident(gcs.attname) || ') = '||gndims||')';
                        constraint_successful := true;
                    EXCEPTION
                        WHEN check_violation THEN
                            RAISE WARNING 'Not inserting ''%'' in ''%.%'' into geometry_columns: could not apply constraint CHECK (st_ndims(%) = %)', quote_ident(gcs.attname), quote_ident(gcs.nspname), quote_ident(gcs.relname), quote_ident(gcs.attname), gndims;
                            gc_is_valid := false;
                    END;
                END IF;
    
                -- Try to apply geometrytype check to column
                IF (gtype IS NOT NULL AND postgis_constraint_type(gcs.nspname, gcs.relname,gcs.attname) IS NULL ) THEN
                    BEGIN
                        EXECUTE 'ALTER TABLE ONLY ' || quote_ident(gcs.nspname) || '.' || quote_ident(gcs.relname) || '
                        ADD CONSTRAINT ' || quote_ident('enforce_geotype_' || gcs.attname) || '
                        CHECK ((geometrytype(' || quote_ident(gcs.attname) || ') = ' || quote_literal(gtype) || ') OR (' || quote_ident(gcs.attname) || ' IS NULL))';
                        constraint_successful := true;
                    EXCEPTION
                        WHEN check_violation THEN
                            -- No geometry check can be applied. This column contains a number of geometry types.
                            RAISE WARNING 'Could not add geometry type check (%) to table column: %.%.%', gtype, quote_ident(gcs.nspname),quote_ident(gcs.relname),quote_ident(gcs.attname);
                    END;
                END IF;
                 --only count if we were successful in applying at least one constraint
                IF constraint_successful THEN
                	inserted := inserted + 1;
                END IF;
            END IF;	        
	    END IF;

	END LOOP;

	RETURN inserted;
END

$$;


ALTER FUNCTION public.populate_geometry_columns(tbl_oid oid, use_typmod boolean) OWNER TO postgres;

--
-- Name: postgis_addbbox(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_addbbox(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_addBBOX';


ALTER FUNCTION public.postgis_addbbox(geometry) OWNER TO postgres;

--
-- Name: postgis_cache_bbox(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_cache_bbox() RETURNS trigger
    LANGUAGE c
    AS '$libdir/postgis-2.0', 'cache_bbox';


ALTER FUNCTION public.postgis_cache_bbox() OWNER TO postgres;

--
-- Name: postgis_constraint_dims(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_constraint_dims(geomschema text, geomtable text, geomcolumn text) RETURNS integer
    LANGUAGE sql STABLE STRICT
    AS $_$
SELECT  replace(split_part(s.consrc, ' = ', 2), ')', '')::integer
		 FROM pg_class c, pg_namespace n, pg_attribute a, pg_constraint s
		 WHERE n.nspname = $1
		 AND c.relname = $2
		 AND a.attname = $3
		 AND a.attrelid = c.oid
		 AND s.connamespace = n.oid
		 AND s.conrelid = c.oid
		 AND a.attnum = ANY (s.conkey)
		 AND s.consrc LIKE '%ndims(% = %';
$_$;


ALTER FUNCTION public.postgis_constraint_dims(geomschema text, geomtable text, geomcolumn text) OWNER TO postgres;

--
-- Name: postgis_constraint_srid(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_constraint_srid(geomschema text, geomtable text, geomcolumn text) RETURNS integer
    LANGUAGE sql STABLE STRICT
    AS $_$
SELECT replace(replace(split_part(s.consrc, ' = ', 2), ')', ''), '(', '')::integer
		 FROM pg_class c, pg_namespace n, pg_attribute a, pg_constraint s
		 WHERE n.nspname = $1
		 AND c.relname = $2
		 AND a.attname = $3
		 AND a.attrelid = c.oid
		 AND s.connamespace = n.oid
		 AND s.conrelid = c.oid
		 AND a.attnum = ANY (s.conkey)
		 AND s.consrc LIKE '%srid(% = %';
$_$;


ALTER FUNCTION public.postgis_constraint_srid(geomschema text, geomtable text, geomcolumn text) OWNER TO postgres;

--
-- Name: postgis_constraint_type(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_constraint_type(geomschema text, geomtable text, geomcolumn text) RETURNS character varying
    LANGUAGE sql STABLE STRICT
    AS $_$
SELECT  replace(split_part(s.consrc, '''', 2), ')', '')::varchar		
		 FROM pg_class c, pg_namespace n, pg_attribute a, pg_constraint s
		 WHERE n.nspname = $1
		 AND c.relname = $2
		 AND a.attname = $3
		 AND a.attrelid = c.oid
		 AND s.connamespace = n.oid
		 AND s.conrelid = c.oid
		 AND a.attnum = ANY (s.conkey)
		 AND s.consrc LIKE '%geometrytype(% = %';
$_$;


ALTER FUNCTION public.postgis_constraint_type(geomschema text, geomtable text, geomcolumn text) OWNER TO postgres;

--
-- Name: postgis_dropbbox(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_dropbbox(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_dropBBOX';


ALTER FUNCTION public.postgis_dropbbox(geometry) OWNER TO postgres;

--
-- Name: postgis_full_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_full_version() RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
	libver text;
	svnver text;
	projver text;
	geosver text;
	gdalver text;
	libxmlver text;
	dbproc text;
	relproc text;
	fullver text;
	rast_lib_ver text;
	rast_scr_ver text;
	topo_scr_ver text;
	json_lib_ver text;
BEGIN
	SELECT postgis_lib_version() INTO libver;
	SELECT postgis_proj_version() INTO projver;
	SELECT postgis_geos_version() INTO geosver;
	SELECT postgis_libjson_version() INTO json_lib_ver;
	BEGIN
		SELECT postgis_gdal_version() INTO gdalver;
	EXCEPTION
		WHEN undefined_function THEN
			gdalver := NULL;
			RAISE NOTICE 'Function postgis_gdal_version() not found.  Is raster support enabled and rtpostgis.sql installed?';
	END;
	SELECT postgis_libxml_version() INTO libxmlver;
	SELECT postgis_scripts_installed() INTO dbproc;
	SELECT postgis_scripts_released() INTO relproc;
	select postgis_svn_version() INTO svnver;
	BEGIN
		SELECT postgis_topology_scripts_installed() INTO topo_scr_ver;
	EXCEPTION
		WHEN undefined_function THEN
			topo_scr_ver := NULL;
			RAISE NOTICE 'Function postgis_topology_scripts_installed() not found. Is topology support enabled and topology.sql installed?';
	END;

	BEGIN
		SELECT postgis_raster_scripts_installed() INTO rast_scr_ver;
	EXCEPTION
		WHEN undefined_function THEN
			rast_scr_ver := NULL;
			RAISE NOTICE 'Function postgis_raster_scripts_installed() not found. Is raster support enabled and rtpostgis.sql installed?';
	END;

	BEGIN
		SELECT postgis_raster_lib_version() INTO rast_lib_ver;
	EXCEPTION
		WHEN undefined_function THEN
			rast_lib_ver := NULL;
			RAISE NOTICE 'Function postgis_raster_lib_version() not found. Is raster support enabled and rtpostgis.sql installed?';
	END;

	fullver = 'POSTGIS="' || libver;

	IF  svnver IS NOT NULL THEN
		fullver = fullver || ' r' || svnver;
	END IF;

	fullver = fullver || '"';

	IF  geosver IS NOT NULL THEN
		fullver = fullver || ' GEOS="' || geosver || '"';
	END IF;

	IF  projver IS NOT NULL THEN
		fullver = fullver || ' PROJ="' || projver || '"';
	END IF;

	IF  gdalver IS NOT NULL THEN
		fullver = fullver || ' GDAL="' || gdalver || '"';
	END IF;

	IF  libxmlver IS NOT NULL THEN
		fullver = fullver || ' LIBXML="' || libxmlver || '"';
	END IF;

	IF json_lib_ver IS NOT NULL THEN
		fullver = fullver || ' LIBJSON="' || json_lib_ver || '"';
	END IF;

	-- fullver = fullver || ' DBPROC="' || dbproc || '"';
	-- fullver = fullver || ' RELPROC="' || relproc || '"';

	IF dbproc != relproc THEN
		fullver = fullver || ' (core procs from "' || dbproc || '" need upgrade)';
	END IF;

	IF topo_scr_ver IS NOT NULL THEN
		fullver = fullver || ' TOPOLOGY';
		IF topo_scr_ver != relproc THEN
			fullver = fullver || ' (topology procs from "' || topo_scr_ver || '" need upgrade)';
		END IF;
	END IF;

	IF rast_lib_ver IS NOT NULL THEN
		fullver = fullver || ' RASTER';
		IF rast_lib_ver != relproc THEN
			fullver = fullver || ' (raster lib from "' || rast_lib_ver || '" need upgrade)';
		END IF;
	END IF;

	IF rast_scr_ver IS NOT NULL AND rast_scr_ver != relproc THEN
		fullver = fullver || ' (raster procs from "' || rast_scr_ver || '" need upgrade)';
	END IF;

	RETURN fullver;
END
$$;


ALTER FUNCTION public.postgis_full_version() OWNER TO postgres;

--
-- Name: postgis_geos_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_geos_version() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_geos_version';


ALTER FUNCTION public.postgis_geos_version() OWNER TO postgres;

--
-- Name: postgis_getbbox(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_getbbox(geometry) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_BOX2D';


ALTER FUNCTION public.postgis_getbbox(geometry) OWNER TO postgres;

--
-- Name: postgis_hasbbox(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_hasbbox(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_hasBBOX';


ALTER FUNCTION public.postgis_hasbbox(geometry) OWNER TO postgres;

--
-- Name: postgis_lib_build_date(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_lib_build_date() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_lib_build_date';


ALTER FUNCTION public.postgis_lib_build_date() OWNER TO postgres;

--
-- Name: postgis_lib_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_lib_version() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_lib_version';


ALTER FUNCTION public.postgis_lib_version() OWNER TO postgres;

--
-- Name: postgis_libjson_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_libjson_version() RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'postgis_libjson_version';


ALTER FUNCTION public.postgis_libjson_version() OWNER TO postgres;

--
-- Name: postgis_libxml_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_libxml_version() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_libxml_version';


ALTER FUNCTION public.postgis_libxml_version() OWNER TO postgres;

--
-- Name: postgis_noop(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_noop(geometry) RETURNS geometry
    LANGUAGE c STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_noop';


ALTER FUNCTION public.postgis_noop(geometry) OWNER TO postgres;

--
-- Name: postgis_proj_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_proj_version() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_proj_version';


ALTER FUNCTION public.postgis_proj_version() OWNER TO postgres;

--
-- Name: postgis_scripts_build_date(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_scripts_build_date() RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $$SELECT '2013-04-11 11:28:13'::text AS version$$;


ALTER FUNCTION public.postgis_scripts_build_date() OWNER TO postgres;

--
-- Name: postgis_scripts_installed(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_scripts_installed() RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $$ SELECT '2.0.3'::text || ' r' || 11128::text AS version $$;


ALTER FUNCTION public.postgis_scripts_installed() OWNER TO postgres;

--
-- Name: postgis_scripts_released(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_scripts_released() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_scripts_released';


ALTER FUNCTION public.postgis_scripts_released() OWNER TO postgres;

--
-- Name: postgis_svn_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_svn_version() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_svn_version';


ALTER FUNCTION public.postgis_svn_version() OWNER TO postgres;

--
-- Name: postgis_transform_geometry(geometry, text, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_transform_geometry(geometry, text, text, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'transform_geom';


ALTER FUNCTION public.postgis_transform_geometry(geometry, text, text, integer) OWNER TO postgres;

--
-- Name: postgis_type_name(character varying, integer, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_type_name(geomname character varying, coord_dimension integer, use_new_name boolean DEFAULT true) RETURNS character varying
    LANGUAGE sql IMMUTABLE STRICT COST 200
    AS $_$
 SELECT CASE WHEN $3 THEN new_name ELSE old_name END As geomname
 	FROM 
 	( VALUES
 		 ('GEOMETRY', 'Geometry', 2) ,
 		 	('GEOMETRY', 'GeometryZ', 3) ,
 		 	('GEOMETRY', 'GeometryZM', 4) ,
			('GEOMETRYCOLLECTION', 'GeometryCollection', 2) ,
			('GEOMETRYCOLLECTION', 'GeometryCollectionZ', 3) ,
			('GEOMETRYCOLLECTIONM', 'GeometryCollectionM', 3) ,
			('GEOMETRYCOLLECTION', 'GeometryCollectionZM', 4) ,
			
			('POINT', 'Point',2) ,
			('POINTM','PointM',3) ,
			('POINT', 'PointZ',3) ,
			('POINT', 'PointZM',4) ,
			
			('MULTIPOINT','MultiPoint',2) ,
			('MULTIPOINT','MultiPointZ',3) ,
			('MULTIPOINTM','MultiPointM',3) ,
			('MULTIPOINT','MultiPointZM',4) ,
			
			('POLYGON', 'Polygon',2) ,
			('POLYGON', 'PolygonZ',3) ,
			('POLYGONM', 'PolygonM',3) ,
			('POLYGON', 'PolygonZM',4) ,
			
			('MULTIPOLYGON', 'MultiPolygon',2) ,
			('MULTIPOLYGON', 'MultiPolygonZ',3) ,
			('MULTIPOLYGONM', 'MultiPolygonM',3) ,
			('MULTIPOLYGON', 'MultiPolygonZM',4) ,
			
			('MULTILINESTRING', 'MultiLineString',2) ,
			('MULTILINESTRING', 'MultiLineStringZ',3) ,
			('MULTILINESTRINGM', 'MultiLineStringM',3) ,
			('MULTILINESTRING', 'MultiLineStringZM',4) ,
			
			('LINESTRING', 'LineString',2) ,
			('LINESTRING', 'LineStringZ',3) ,
			('LINESTRINGM', 'LineStringM',3) ,
			('LINESTRING', 'LineStringZM',4) ,
			
			('CIRCULARSTRING', 'CircularString',2) ,
			('CIRCULARSTRING', 'CircularStringZ',3) ,
			('CIRCULARSTRINGM', 'CircularStringM',3) ,
			('CIRCULARSTRING', 'CircularStringZM',4) ,
			
			('COMPOUNDCURVE', 'CompoundCurve',2) ,
			('COMPOUNDCURVE', 'CompoundCurveZ',3) ,
			('COMPOUNDCURVEM', 'CompoundCurveM',3) ,
			('COMPOUNDCURVE', 'CompoundCurveZM',4) ,
			
			('CURVEPOLYGON', 'CurvePolygon',2) ,
			('CURVEPOLYGON', 'CurvePolygonZ',3) ,
			('CURVEPOLYGONM', 'CurvePolygonM',3) ,
			('CURVEPOLYGON', 'CurvePolygonZM',4) ,
			
			('MULTICURVE', 'MultiCurve',2 ) ,
			('MULTICURVE', 'MultiCurveZ',3 ) ,
			('MULTICURVEM', 'MultiCurveM',3 ) ,
			('MULTICURVE', 'MultiCurveZM',4 ) ,
			
			('MULTISURFACE', 'MultiSurface', 2) ,
			('MULTISURFACE', 'MultiSurfaceZ', 3) ,
			('MULTISURFACEM', 'MultiSurfaceM', 3) ,
			('MULTISURFACE', 'MultiSurfaceZM', 4) ,
			
			('POLYHEDRALSURFACE', 'PolyhedralSurface',2) ,
			('POLYHEDRALSURFACE', 'PolyhedralSurfaceZ',3) ,
			('POLYHEDRALSURFACEM', 'PolyhedralSurfaceM',3) ,
			('POLYHEDRALSURFACE', 'PolyhedralSurfaceZM',4) ,
			
			('TRIANGLE', 'Triangle',2) ,
			('TRIANGLE', 'TriangleZ',3) ,
			('TRIANGLEM', 'TriangleM',3) ,
			('TRIANGLE', 'TriangleZM',4) ,

			('TIN', 'Tin', 2),
			('TIN', 'TinZ', 3),
			('TIN', 'TinM', 3),
			('TIN', 'TinZM', 4) )
			 As g(old_name, new_name, coord_dimension)
		WHERE (upper(old_name) = upper($1) OR upper(new_name) = upper($1))
			AND coord_dimension = $2;
$_$;


ALTER FUNCTION public.postgis_type_name(geomname character varying, coord_dimension integer, use_new_name boolean) OWNER TO postgres;

--
-- Name: postgis_typmod_dims(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_typmod_dims(integer) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'postgis_typmod_dims';


ALTER FUNCTION public.postgis_typmod_dims(integer) OWNER TO postgres;

--
-- Name: postgis_typmod_srid(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_typmod_srid(integer) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'postgis_typmod_srid';


ALTER FUNCTION public.postgis_typmod_srid(integer) OWNER TO postgres;

--
-- Name: postgis_typmod_type(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_typmod_type(integer) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'postgis_typmod_type';


ALTER FUNCTION public.postgis_typmod_type(integer) OWNER TO postgres;

--
-- Name: postgis_version(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION postgis_version() RETURNS text
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'postgis_version';


ALTER FUNCTION public.postgis_version() OWNER TO postgres;

--
-- Name: st_3dclosestpoint(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dclosestpoint(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_closestpoint3d';


ALTER FUNCTION public.st_3dclosestpoint(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_3ddfullywithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3ddfullywithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && ST_Expand($2,$3) AND $2 && ST_Expand($1,$3) AND _ST_3DDFullyWithin($1, $2, $3)$_$;


ALTER FUNCTION public.st_3ddfullywithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: st_3ddistance(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3ddistance(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_mindistance3d';


ALTER FUNCTION public.st_3ddistance(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_3ddwithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3ddwithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && ST_Expand($2,$3) AND $2 && ST_Expand($1,$3) AND _ST_3DDWithin($1, $2, $3)$_$;


ALTER FUNCTION public.st_3ddwithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: st_3dintersects(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dintersects(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_3DDWithin($1, $2, 0.0)$_$;


ALTER FUNCTION public.st_3dintersects(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_3dlength(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dlength(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_length_linestring';


ALTER FUNCTION public.st_3dlength(geometry) OWNER TO postgres;

--
-- Name: st_3dlength_spheroid(geometry, spheroid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dlength_spheroid(geometry, spheroid) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_length_ellipsoid_linestring';


ALTER FUNCTION public.st_3dlength_spheroid(geometry, spheroid) OWNER TO postgres;

--
-- Name: st_3dlongestline(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dlongestline(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_longestline3d';


ALTER FUNCTION public.st_3dlongestline(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_3dmakebox(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dmakebox(geom1 geometry, geom2 geometry) RETURNS box3d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_construct';


ALTER FUNCTION public.st_3dmakebox(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_3dmaxdistance(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dmaxdistance(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_maxdistance3d';


ALTER FUNCTION public.st_3dmaxdistance(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_3dperimeter(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dperimeter(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_perimeter_poly';


ALTER FUNCTION public.st_3dperimeter(geometry) OWNER TO postgres;

--
-- Name: st_3dshortestline(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_3dshortestline(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_shortestline3d';


ALTER FUNCTION public.st_3dshortestline(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_addmeasure(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_addmeasure(geometry, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_AddMeasure';


ALTER FUNCTION public.st_addmeasure(geometry, double precision, double precision) OWNER TO postgres;

--
-- Name: st_addpoint(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_addpoint(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_addpoint';


ALTER FUNCTION public.st_addpoint(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_addpoint(geometry, geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_addpoint(geom1 geometry, geom2 geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_addpoint';


ALTER FUNCTION public.st_addpoint(geom1 geometry, geom2 geometry, integer) OWNER TO postgres;

--
-- Name: st_affine(geometry, double precision, double precision, double precision, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_affine(geometry, double precision, double precision, double precision, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  $2, $3, 0,  $4, $5, 0,  0, 0, 1,  $6, $7, 0)$_$;


ALTER FUNCTION public.st_affine(geometry, double precision, double precision, double precision, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_affine(geometry, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_affine(geometry, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_affine';


ALTER FUNCTION public.st_affine(geometry, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_area(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_area(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_area_polygon';


ALTER FUNCTION public.st_area(geometry) OWNER TO postgres;

--
-- Name: st_area(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_area(text) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_Area($1::geometry);  $_$;


ALTER FUNCTION public.st_area(text) OWNER TO postgres;

--
-- Name: st_area(geography, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_area(geog geography, use_spheroid boolean DEFAULT true) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_area';


ALTER FUNCTION public.st_area(geog geography, use_spheroid boolean) OWNER TO postgres;

--
-- Name: st_area2d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_area2d(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_area_polygon';


ALTER FUNCTION public.st_area2d(geometry) OWNER TO postgres;

--
-- Name: st_asbinary(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asbinary(geometry) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asBinary';


ALTER FUNCTION public.st_asbinary(geometry) OWNER TO postgres;

--
-- Name: st_asbinary(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asbinary(geography) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asBinary';


ALTER FUNCTION public.st_asbinary(geography) OWNER TO postgres;

--
-- Name: st_asbinary(geometry, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asbinary(geometry, text) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asBinary';


ALTER FUNCTION public.st_asbinary(geometry, text) OWNER TO postgres;

--
-- Name: st_asbinary(geography, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asbinary(geography, text) RETURNS bytea
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_AsBinary($1::geometry, $2);  $_$;


ALTER FUNCTION public.st_asbinary(geography, text) OWNER TO postgres;

--
-- Name: st_asewkb(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asewkb(geometry) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'WKBFromLWGEOM';


ALTER FUNCTION public.st_asewkb(geometry) OWNER TO postgres;

--
-- Name: st_asewkb(geometry, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asewkb(geometry, text) RETURNS bytea
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'WKBFromLWGEOM';


ALTER FUNCTION public.st_asewkb(geometry, text) OWNER TO postgres;

--
-- Name: st_asewkt(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asewkt(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asEWKT';


ALTER FUNCTION public.st_asewkt(geometry) OWNER TO postgres;

--
-- Name: st_asewkt(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asewkt(geography) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asEWKT';


ALTER FUNCTION public.st_asewkt(geography) OWNER TO postgres;

--
-- Name: st_asewkt(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asewkt(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_AsEWKT($1::geometry);  $_$;


ALTER FUNCTION public.st_asewkt(text) OWNER TO postgres;

--
-- Name: st_asgeojson(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgeojson(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGeoJson(1, $1::geometry,15,0);  $_$;


ALTER FUNCTION public.st_asgeojson(text) OWNER TO postgres;

--
-- Name: st_asgeojson(geometry, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgeojson(geom geometry, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGeoJson(1, $1, $2, $3); $_$;


ALTER FUNCTION public.st_asgeojson(geom geometry, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_asgeojson(geography, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgeojson(geog geography, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGeoJson(1, $1, $2, $3); $_$;


ALTER FUNCTION public.st_asgeojson(geog geography, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_asgeojson(integer, geometry, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgeojson(gj_version integer, geom geometry, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGeoJson($1, $2, $3, $4); $_$;


ALTER FUNCTION public.st_asgeojson(gj_version integer, geom geometry, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_asgeojson(integer, geography, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgeojson(gj_version integer, geog geography, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGeoJson($1, $2, $3, $4); $_$;


ALTER FUNCTION public.st_asgeojson(gj_version integer, geog geography, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_asgml(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgml(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGML(2,$1::geometry,15,0, NULL);  $_$;


ALTER FUNCTION public.st_asgml(text) OWNER TO postgres;

--
-- Name: st_asgml(geometry, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgml(geom geometry, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsGML(2, $1, $2, $3, null); $_$;


ALTER FUNCTION public.st_asgml(geom geometry, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_asgml(geography, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgml(geog geography, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_AsGML(2, $1, $2, $3, null)$_$;


ALTER FUNCTION public.st_asgml(geog geography, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_asgml(integer, geometry, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgml(version integer, geom geometry, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0, nprefix text DEFAULT NULL::text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT _ST_AsGML($1, $2, $3, $4,$5); $_$;


ALTER FUNCTION public.st_asgml(version integer, geom geometry, maxdecimaldigits integer, options integer, nprefix text) OWNER TO postgres;

--
-- Name: st_asgml(integer, geography, integer, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asgml(version integer, geog geography, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0, nprefix text DEFAULT NULL::text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT _ST_AsGML($1, $2, $3, $4, $5);$_$;


ALTER FUNCTION public.st_asgml(version integer, geog geography, maxdecimaldigits integer, options integer, nprefix text) OWNER TO postgres;

--
-- Name: st_ashexewkb(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_ashexewkb(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asHEXEWKB';


ALTER FUNCTION public.st_ashexewkb(geometry) OWNER TO postgres;

--
-- Name: st_ashexewkb(geometry, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_ashexewkb(geometry, text) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asHEXEWKB';


ALTER FUNCTION public.st_ashexewkb(geometry, text) OWNER TO postgres;

--
-- Name: st_askml(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_askml(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsKML(2, $1::geometry, 15, null);  $_$;


ALTER FUNCTION public.st_askml(text) OWNER TO postgres;

--
-- Name: st_askml(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_askml(geom geometry, maxdecimaldigits integer DEFAULT 15) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_AsKML(2, ST_Transform($1,4326), $2, null); $_$;


ALTER FUNCTION public.st_askml(geom geometry, maxdecimaldigits integer) OWNER TO postgres;

--
-- Name: st_askml(geography, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_askml(geog geography, maxdecimaldigits integer DEFAULT 15) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_AsKML(2, $1, $2, null)$_$;


ALTER FUNCTION public.st_askml(geog geography, maxdecimaldigits integer) OWNER TO postgres;

--
-- Name: st_askml(integer, geometry, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_askml(version integer, geom geometry, maxdecimaldigits integer DEFAULT 15, nprefix text DEFAULT NULL::text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT _ST_AsKML($1, ST_Transform($2,4326), $3, $4); $_$;


ALTER FUNCTION public.st_askml(version integer, geom geometry, maxdecimaldigits integer, nprefix text) OWNER TO postgres;

--
-- Name: st_askml(integer, geography, integer, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_askml(version integer, geog geography, maxdecimaldigits integer DEFAULT 15, nprefix text DEFAULT NULL::text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT _ST_AsKML($1, $2, $3, $4)$_$;


ALTER FUNCTION public.st_askml(version integer, geog geography, maxdecimaldigits integer, nprefix text) OWNER TO postgres;

--
-- Name: st_aslatlontext(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_aslatlontext(geometry) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_AsLatLonText($1, '') $_$;


ALTER FUNCTION public.st_aslatlontext(geometry) OWNER TO postgres;

--
-- Name: st_aslatlontext(geometry, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_aslatlontext(geometry, text) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_latlon';


ALTER FUNCTION public.st_aslatlontext(geometry, text) OWNER TO postgres;

--
-- Name: st_assvg(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_assvg(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_AsSVG($1::geometry,0,15);  $_$;


ALTER FUNCTION public.st_assvg(text) OWNER TO postgres;

--
-- Name: st_assvg(geometry, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_assvg(geom geometry, rel integer DEFAULT 0, maxdecimaldigits integer DEFAULT 15) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asSVG';


ALTER FUNCTION public.st_assvg(geom geometry, rel integer, maxdecimaldigits integer) OWNER TO postgres;

--
-- Name: st_assvg(geography, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_assvg(geog geography, rel integer DEFAULT 0, maxdecimaldigits integer DEFAULT 15) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_as_svg';


ALTER FUNCTION public.st_assvg(geog geography, rel integer, maxdecimaldigits integer) OWNER TO postgres;

--
-- Name: st_astext(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_astext(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asText';


ALTER FUNCTION public.st_astext(geometry) OWNER TO postgres;

--
-- Name: st_astext(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_astext(geography) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_asText';


ALTER FUNCTION public.st_astext(geography) OWNER TO postgres;

--
-- Name: st_astext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_astext(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_AsText($1::geometry);  $_$;


ALTER FUNCTION public.st_astext(text) OWNER TO postgres;

--
-- Name: st_asx3d(geometry, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_asx3d(geom geometry, maxdecimaldigits integer DEFAULT 15, options integer DEFAULT 0) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT _ST_AsX3D(3,$1,$2,$3,'');$_$;


ALTER FUNCTION public.st_asx3d(geom geometry, maxdecimaldigits integer, options integer) OWNER TO postgres;

--
-- Name: st_azimuth(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_azimuth(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_azimuth';


ALTER FUNCTION public.st_azimuth(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_azimuth(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_azimuth(geog1 geography, geog2 geography) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_azimuth';


ALTER FUNCTION public.st_azimuth(geog1 geography, geog2 geography) OWNER TO postgres;

--
-- Name: st_bdmpolyfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_bdmpolyfromtext(text, integer) RETURNS geometry
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
	geomtext alias for $1;
	srid alias for $2;
	mline geometry;
	geom geometry;
BEGIN
	mline := ST_MultiLineStringFromText(geomtext, srid);

	IF mline IS NULL
	THEN
		RAISE EXCEPTION 'Input is not a MultiLinestring';
	END IF;

	geom := ST_Multi(ST_BuildArea(mline));

	RETURN geom;
END;
$_$;


ALTER FUNCTION public.st_bdmpolyfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_bdpolyfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_bdpolyfromtext(text, integer) RETURNS geometry
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
	geomtext alias for $1;
	srid alias for $2;
	mline geometry;
	geom geometry;
BEGIN
	mline := ST_MultiLineStringFromText(geomtext, srid);

	IF mline IS NULL
	THEN
		RAISE EXCEPTION 'Input is not a MultiLinestring';
	END IF;

	geom := ST_BuildArea(mline);

	IF GeometryType(geom) != 'POLYGON'
	THEN
		RAISE EXCEPTION 'Input returns more then a single polygon, try using BdMPolyFromText instead';
	END IF;

	RETURN geom;
END;
$_$;


ALTER FUNCTION public.st_bdpolyfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_boundary(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_boundary(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'boundary';


ALTER FUNCTION public.st_boundary(geometry) OWNER TO postgres;

--
-- Name: st_buffer(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_buffer(geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'buffer';


ALTER FUNCTION public.st_buffer(geometry, double precision) OWNER TO postgres;

--
-- Name: st_buffer(geography, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_buffer(geography, double precision) RETURNS geography
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT geography(ST_Transform(ST_Buffer(ST_Transform(geometry($1), _ST_BestSRID($1)), $2), 4326))$_$;


ALTER FUNCTION public.st_buffer(geography, double precision) OWNER TO postgres;

--
-- Name: st_buffer(text, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_buffer(text, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_Buffer($1::geometry, $2);  $_$;


ALTER FUNCTION public.st_buffer(text, double precision) OWNER TO postgres;

--
-- Name: st_buffer(geometry, double precision, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_buffer(geometry, double precision, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_Buffer($1, $2,
		CAST('quad_segs='||CAST($3 AS text) as cstring))
	   $_$;


ALTER FUNCTION public.st_buffer(geometry, double precision, integer) OWNER TO postgres;

--
-- Name: st_buffer(geometry, double precision, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_buffer(geometry, double precision, text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT _ST_Buffer($1, $2,
		CAST( regexp_replace($3, '^[0123456789]+$',
			'quad_segs='||$3) AS cstring)
		)
	   $_$;


ALTER FUNCTION public.st_buffer(geometry, double precision, text) OWNER TO postgres;

--
-- Name: st_buildarea(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_buildarea(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_BuildArea';


ALTER FUNCTION public.st_buildarea(geometry) OWNER TO postgres;

--
-- Name: st_centroid(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_centroid(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'centroid';


ALTER FUNCTION public.st_centroid(geometry) OWNER TO postgres;

--
-- Name: st_cleangeometry(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_cleangeometry(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_CleanGeometry';


ALTER FUNCTION public.st_cleangeometry(geometry) OWNER TO postgres;

--
-- Name: st_closestpoint(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_closestpoint(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_closestpoint';


ALTER FUNCTION public.st_closestpoint(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_collect(geometry[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_collect(geometry[]) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_collect_garray';


ALTER FUNCTION public.st_collect(geometry[]) OWNER TO postgres;

--
-- Name: st_collect(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_collect(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'LWGEOM_collect';


ALTER FUNCTION public.st_collect(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_collectionextract(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_collectionextract(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_CollectionExtract';


ALTER FUNCTION public.st_collectionextract(geometry, integer) OWNER TO postgres;

--
-- Name: st_collectionhomogenize(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_collectionhomogenize(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_CollectionHomogenize';


ALTER FUNCTION public.st_collectionhomogenize(geometry) OWNER TO postgres;

--
-- Name: st_combine_bbox(box2d, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_combine_bbox(box2d, geometry) RETURNS box2d
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'BOX2D_combine';


ALTER FUNCTION public.st_combine_bbox(box2d, geometry) OWNER TO postgres;

--
-- Name: st_combine_bbox(box3d, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_combine_bbox(box3d, geometry) RETURNS box3d
    LANGUAGE c IMMUTABLE
    AS '$libdir/postgis-2.0', 'BOX3D_combine';


ALTER FUNCTION public.st_combine_bbox(box3d, geometry) OWNER TO postgres;

--
-- Name: st_concavehull(geometry, double precision, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_concavehull(param_geom geometry, param_pctconvex double precision, param_allow_holes boolean DEFAULT false) RETURNS geometry
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
	DECLARE
		var_convhull geometry := ST_ConvexHull(param_geom);
		var_param_geom geometry := param_geom;
		var_initarea float := ST_Area(var_convhull);
		var_newarea float := var_initarea;
		var_div integer := 6; 
		var_tempgeom geometry;
		var_tempgeom2 geometry;
		var_cent geometry;
		var_geoms geometry[4]; 
		var_enline geometry;
		var_resultgeom geometry;
		var_atempgeoms geometry[];
		var_buf float := 1; 
	BEGIN
		-- We start with convex hull as our base
		var_resultgeom := var_convhull;
		
		IF param_pctconvex = 1 THEN
			return var_resultgeom;
		ELSIF ST_GeometryType(var_param_geom) = 'ST_Polygon' THEN -- it is as concave as it is going to get
			IF param_allow_holes THEN -- leave the holes
				RETURN var_param_geom;
			ELSE -- remove the holes
				var_resultgeom := ST_MakePolygon(ST_ExteriorRing(var_param_geom));
				RETURN var_resultgeom;
			END IF;
		END IF;
		IF ST_Dimension(var_resultgeom) > 1 AND param_pctconvex BETWEEN 0 and 0.98 THEN
		-- get linestring that forms envelope of geometry
			var_enline := ST_Boundary(ST_Envelope(var_param_geom));
			var_buf := ST_Length(var_enline)/1000.0;
			IF ST_GeometryType(var_param_geom) = 'ST_MultiPoint' AND ST_NumGeometries(var_param_geom) BETWEEN 4 and 200 THEN
			-- we make polygons out of points since they are easier to cave in. 
			-- Note we limit to between 4 and 200 points because this process is slow and gets quadratically slow
				var_buf := sqrt(ST_Area(var_convhull)*0.8/(ST_NumGeometries(var_param_geom)*ST_NumGeometries(var_param_geom)));
				var_atempgeoms := ARRAY(SELECT geom FROM ST_DumpPoints(var_param_geom));
				-- 5 and 10 and just fudge factors
				var_tempgeom := ST_Union(ARRAY(SELECT geom
						FROM (
						-- fuse near neighbors together
						SELECT DISTINCT ON (i) i,  ST_Distance(var_atempgeoms[i],var_atempgeoms[j]), ST_Buffer(ST_MakeLine(var_atempgeoms[i], var_atempgeoms[j]) , var_buf*5, 'quad_segs=3') As geom
								FROM generate_series(1,array_upper(var_atempgeoms, 1)) As i
									INNER JOIN generate_series(1,array_upper(var_atempgeoms, 1)) As j 
										ON (
								 NOT ST_Intersects(var_atempgeoms[i],var_atempgeoms[j])
									AND ST_DWithin(var_atempgeoms[i],var_atempgeoms[j], var_buf*10)
									)
								UNION ALL
						-- catch the ones with no near neighbors
								SELECT i, 0, ST_Buffer(var_atempgeoms[i] , var_buf*10, 'quad_segs=3') As geom
								FROM generate_series(1,array_upper(var_atempgeoms, 1)) As i
									LEFT JOIN generate_series(ceiling(array_upper(var_atempgeoms,1)/2)::integer,array_upper(var_atempgeoms, 1)) As j 
										ON (
								 NOT ST_Intersects(var_atempgeoms[i],var_atempgeoms[j])
									AND ST_DWithin(var_atempgeoms[i],var_atempgeoms[j], var_buf*10) 
									)
									WHERE j IS NULL
								ORDER BY 1, 2
							) As foo	) );
				IF ST_IsValid(var_tempgeom) AND ST_GeometryType(var_tempgeom) = 'ST_Polygon' THEN
					var_tempgeom := ST_Intersection(var_tempgeom, var_convhull);
					IF param_allow_holes THEN
						var_param_geom := var_tempgeom;
					ELSE
						var_param_geom := ST_MakePolygon(ST_ExteriorRing(var_tempgeom));
					END IF;
					return var_param_geom;
				ELSIF ST_IsValid(var_tempgeom) THEN
					var_param_geom := ST_Intersection(var_tempgeom, var_convhull);	
				END IF;
			END IF;

			IF ST_GeometryType(var_param_geom) = 'ST_Polygon' THEN
				IF NOT param_allow_holes THEN
					var_param_geom := ST_MakePolygon(ST_ExteriorRing(var_param_geom));
				END IF;
				return var_param_geom;
			END IF;
            var_cent := ST_Centroid(var_param_geom);
            IF (ST_XMax(var_enline) - ST_XMin(var_enline) ) > var_buf AND (ST_YMax(var_enline) - ST_YMin(var_enline) ) > var_buf THEN
                    IF ST_Dwithin(ST_Centroid(var_convhull) , ST_Centroid(ST_Envelope(var_param_geom)), var_buf/2) THEN
                -- If the geometric dimension is > 1 and the object is symettric (cutting at centroid will not work -- offset a bit)
                        var_cent := ST_Translate(var_cent, (ST_XMax(var_enline) - ST_XMin(var_enline))/1000,  (ST_YMAX(var_enline) - ST_YMin(var_enline))/1000);
                    ELSE
                        -- uses closest point on geometry to centroid. I can't explain why we are doing this
                        var_cent := ST_ClosestPoint(var_param_geom,var_cent);
                    END IF;
                    IF ST_DWithin(var_cent, var_enline,var_buf) THEN
                        var_cent := ST_centroid(ST_Envelope(var_param_geom));
                    END IF;
                    -- break envelope into 4 triangles about the centroid of the geometry and returned the clipped geometry in each quadrant
                    FOR i in 1 .. 4 LOOP
                       var_geoms[i] := ST_MakePolygon(ST_MakeLine(ARRAY[ST_PointN(var_enline,i), ST_PointN(var_enline,i+1), var_cent, ST_PointN(var_enline,i)]));
                       var_geoms[i] := ST_Intersection(var_param_geom, ST_Buffer(var_geoms[i],var_buf));
                       IF ST_IsValid(var_geoms[i]) THEN 
                            
                       ELSE
                            var_geoms[i] := ST_BuildArea(ST_MakeLine(ARRAY[ST_PointN(var_enline,i), ST_PointN(var_enline,i+1), var_cent, ST_PointN(var_enline,i)]));
                       END IF; 
                    END LOOP;
                    var_tempgeom := ST_Union(ARRAY[ST_ConvexHull(var_geoms[1]), ST_ConvexHull(var_geoms[2]) , ST_ConvexHull(var_geoms[3]), ST_ConvexHull(var_geoms[4])]); 
                    --RAISE NOTICE 'Curr vex % ', ST_AsText(var_tempgeom);
                    IF ST_Area(var_tempgeom) <= var_newarea AND ST_IsValid(var_tempgeom)  THEN --AND ST_GeometryType(var_tempgeom) ILIKE '%Polygon'
                        
                        var_tempgeom := ST_Buffer(ST_ConcaveHull(var_geoms[1],least(param_pctconvex + param_pctconvex/var_div),true),var_buf, 'quad_segs=2');
                        FOR i IN 1 .. 4 LOOP
                            var_geoms[i] := ST_Buffer(ST_ConcaveHull(var_geoms[i],least(param_pctconvex + param_pctconvex/var_div),true), var_buf, 'quad_segs=2');
                            IF ST_IsValid(var_geoms[i]) Then
                                var_tempgeom := ST_Union(var_tempgeom, var_geoms[i]);
                            ELSE
                                RAISE NOTICE 'Not valid % %', i, ST_AsText(var_tempgeom);
                                var_tempgeom := ST_Union(var_tempgeom, ST_ConvexHull(var_geoms[i]));
                            END IF; 
                        END LOOP;

                        --RAISE NOTICE 'Curr concave % ', ST_AsText(var_tempgeom);
                        IF ST_IsValid(var_tempgeom) THEN
                            var_resultgeom := var_tempgeom;
                        END IF;
                        var_newarea := ST_Area(var_resultgeom);
                    ELSIF ST_IsValid(var_tempgeom) THEN
                        var_resultgeom := var_tempgeom;
                    END IF;

                    IF ST_NumGeometries(var_resultgeom) > 1  THEN
                        var_tempgeom := _ST_ConcaveHull(var_resultgeom);
                        IF ST_IsValid(var_tempgeom) AND ST_GeometryType(var_tempgeom) ILIKE 'ST_Polygon' THEN
                            var_resultgeom := var_tempgeom;
                        ELSE
                            var_resultgeom := ST_Buffer(var_tempgeom,var_buf, 'quad_segs=2');
                        END IF;
                    END IF;
                    IF param_allow_holes = false THEN 
                    -- only keep exterior ring since we do not want holes
                        var_resultgeom := ST_MakePolygon(ST_ExteriorRing(var_resultgeom));
                    END IF;
                ELSE
                    var_resultgeom := ST_Buffer(var_resultgeom,var_buf);
                END IF;
                var_resultgeom := ST_Intersection(var_resultgeom, ST_ConvexHull(var_param_geom));
            ELSE
                -- dimensions are too small to cut
                var_resultgeom := _ST_ConcaveHull(var_param_geom);
            END IF;
            RETURN var_resultgeom;
	END;
$$;


ALTER FUNCTION public.st_concavehull(param_geom geometry, param_pctconvex double precision, param_allow_holes boolean) OWNER TO postgres;

--
-- Name: st_contains(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_contains(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Contains($1,$2)$_$;


ALTER FUNCTION public.st_contains(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_containsproperly(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_containsproperly(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_ContainsProperly($1,$2)$_$;


ALTER FUNCTION public.st_containsproperly(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_convexhull(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_convexhull(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'convexhull';


ALTER FUNCTION public.st_convexhull(geometry) OWNER TO postgres;

--
-- Name: st_coorddim(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_coorddim(geometry geometry) RETURNS smallint
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_ndims';


ALTER FUNCTION public.st_coorddim(geometry geometry) OWNER TO postgres;

--
-- Name: st_coveredby(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_coveredby(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_CoveredBy($1,$2)$_$;


ALTER FUNCTION public.st_coveredby(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_coveredby(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_coveredby(geography, geography) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Covers($2, $1)$_$;


ALTER FUNCTION public.st_coveredby(geography, geography) OWNER TO postgres;

--
-- Name: st_coveredby(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_coveredby(text, text) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT ST_CoveredBy($1::geometry, $2::geometry);  $_$;


ALTER FUNCTION public.st_coveredby(text, text) OWNER TO postgres;

--
-- Name: st_covers(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_covers(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Covers($1,$2)$_$;


ALTER FUNCTION public.st_covers(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_covers(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_covers(geography, geography) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Covers($1, $2)$_$;


ALTER FUNCTION public.st_covers(geography, geography) OWNER TO postgres;

--
-- Name: st_covers(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_covers(text, text) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT ST_Covers($1::geometry, $2::geometry);  $_$;


ALTER FUNCTION public.st_covers(text, text) OWNER TO postgres;

--
-- Name: st_crosses(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_crosses(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Crosses($1,$2)$_$;


ALTER FUNCTION public.st_crosses(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_curvetoline(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_curvetoline(geometry) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_CurveToLine($1, 32)$_$;


ALTER FUNCTION public.st_curvetoline(geometry) OWNER TO postgres;

--
-- Name: st_curvetoline(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_curvetoline(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_curve_segmentize';


ALTER FUNCTION public.st_curvetoline(geometry, integer) OWNER TO postgres;

--
-- Name: st_dfullywithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dfullywithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && ST_Expand($2,$3) AND $2 && ST_Expand($1,$3) AND _ST_DFullyWithin(ST_ConvexHull($1), ST_ConvexHull($2), $3)$_$;


ALTER FUNCTION public.st_dfullywithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: st_difference(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_difference(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'difference';


ALTER FUNCTION public.st_difference(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_dimension(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dimension(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_dimension';


ALTER FUNCTION public.st_dimension(geometry) OWNER TO postgres;

--
-- Name: st_disjoint(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_disjoint(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'disjoint';


ALTER FUNCTION public.st_disjoint(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_distance(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_distance(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_mindistance2d';


ALTER FUNCTION public.st_distance(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_distance(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_distance(geography, geography) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_Distance($1, $2, 0.0, true)$_$;


ALTER FUNCTION public.st_distance(geography, geography) OWNER TO postgres;

--
-- Name: st_distance(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_distance(text, text) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_Distance($1::geometry, $2::geometry);  $_$;


ALTER FUNCTION public.st_distance(text, text) OWNER TO postgres;

--
-- Name: st_distance(geography, geography, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_distance(geography, geography, boolean) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_Distance($1, $2, 0.0, $3)$_$;


ALTER FUNCTION public.st_distance(geography, geography, boolean) OWNER TO postgres;

--
-- Name: st_distance_sphere(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_distance_sphere(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT COST 300
    AS $_$
	select st_distance(geography($1),geography($2),false)
	$_$;


ALTER FUNCTION public.st_distance_sphere(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_distance_spheroid(geometry, geometry, spheroid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_distance_spheroid(geom1 geometry, geom2 geometry, spheroid) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_distance_ellipsoid';


ALTER FUNCTION public.st_distance_spheroid(geom1 geometry, geom2 geometry, spheroid) OWNER TO postgres;

--
-- Name: st_dump(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dump(geometry) RETURNS SETOF geometry_dump
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_dump';


ALTER FUNCTION public.st_dump(geometry) OWNER TO postgres;

--
-- Name: st_dumppoints(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dumppoints(geometry) RETURNS SETOF geometry_dump
    LANGUAGE sql STRICT
    AS $_$
  SELECT * FROM _ST_DumpPoints($1, NULL);
$_$;


ALTER FUNCTION public.st_dumppoints(geometry) OWNER TO postgres;

--
-- Name: st_dumprings(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dumprings(geometry) RETURNS SETOF geometry_dump
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_dump_rings';


ALTER FUNCTION public.st_dumprings(geometry) OWNER TO postgres;

--
-- Name: st_dwithin(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dwithin(geom1 geometry, geom2 geometry, double precision) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && ST_Expand($2,$3) AND $2 && ST_Expand($1,$3) AND _ST_DWithin($1, $2, $3)$_$;


ALTER FUNCTION public.st_dwithin(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: st_dwithin(geography, geography, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dwithin(geography, geography, double precision) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && _ST_Expand($2,$3) AND $2 && _ST_Expand($1,$3) AND _ST_DWithin($1, $2, $3, true)$_$;


ALTER FUNCTION public.st_dwithin(geography, geography, double precision) OWNER TO postgres;

--
-- Name: st_dwithin(text, text, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dwithin(text, text, double precision) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT ST_DWithin($1::geometry, $2::geometry, $3);  $_$;


ALTER FUNCTION public.st_dwithin(text, text, double precision) OWNER TO postgres;

--
-- Name: st_dwithin(geography, geography, double precision, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_dwithin(geography, geography, double precision, boolean) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && _ST_Expand($2,$3) AND $2 && _ST_Expand($1,$3) AND _ST_DWithin($1, $2, $3, $4)$_$;


ALTER FUNCTION public.st_dwithin(geography, geography, double precision, boolean) OWNER TO postgres;

--
-- Name: st_endpoint(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_endpoint(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_endpoint_linestring';


ALTER FUNCTION public.st_endpoint(geometry) OWNER TO postgres;

--
-- Name: st_envelope(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_envelope(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_envelope';


ALTER FUNCTION public.st_envelope(geometry) OWNER TO postgres;

--
-- Name: st_equals(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_equals(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 ~= $2 AND _ST_Equals($1,$2)$_$;


ALTER FUNCTION public.st_equals(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_estimated_extent(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_estimated_extent(text, text) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT SECURITY DEFINER
    AS '$libdir/postgis-2.0', 'geometry_estimated_extent';


ALTER FUNCTION public.st_estimated_extent(text, text) OWNER TO postgres;

--
-- Name: st_estimated_extent(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_estimated_extent(text, text, text) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT SECURITY DEFINER
    AS '$libdir/postgis-2.0', 'geometry_estimated_extent';


ALTER FUNCTION public.st_estimated_extent(text, text, text) OWNER TO postgres;

--
-- Name: st_expand(box2d, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_expand(box2d, double precision) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX2D_expand';


ALTER FUNCTION public.st_expand(box2d, double precision) OWNER TO postgres;

--
-- Name: st_expand(box3d, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_expand(box3d, double precision) RETURNS box3d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_expand';


ALTER FUNCTION public.st_expand(box3d, double precision) OWNER TO postgres;

--
-- Name: st_expand(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_expand(geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_expand';


ALTER FUNCTION public.st_expand(geometry, double precision) OWNER TO postgres;

--
-- Name: st_exteriorring(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_exteriorring(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_exteriorring_polygon';


ALTER FUNCTION public.st_exteriorring(geometry) OWNER TO postgres;

--
-- Name: st_find_extent(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_find_extent(text, text) RETURNS box2d
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
	tablename alias for $1;
	columnname alias for $2;
	myrec RECORD;

BEGIN
	FOR myrec IN EXECUTE 'SELECT ST_Extent("' || columnname || '") As extent FROM "' || tablename || '"' LOOP
		return myrec.extent;
	END LOOP;
END;
$_$;


ALTER FUNCTION public.st_find_extent(text, text) OWNER TO postgres;

--
-- Name: st_find_extent(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_find_extent(text, text, text) RETURNS box2d
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
	schemaname alias for $1;
	tablename alias for $2;
	columnname alias for $3;
	myrec RECORD;

BEGIN
	FOR myrec IN EXECUTE 'SELECT ST_Extent("' || columnname || '") As extent FROM "' || schemaname || '"."' || tablename || '"' LOOP
		return myrec.extent;
	END LOOP;
END;
$_$;


ALTER FUNCTION public.st_find_extent(text, text, text) OWNER TO postgres;

--
-- Name: st_flipcoordinates(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_flipcoordinates(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_FlipCoordinates';


ALTER FUNCTION public.st_flipcoordinates(geometry) OWNER TO postgres;

--
-- Name: st_force_2d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_force_2d(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_2d';


ALTER FUNCTION public.st_force_2d(geometry) OWNER TO postgres;

--
-- Name: st_force_3d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_force_3d(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_3dz';


ALTER FUNCTION public.st_force_3d(geometry) OWNER TO postgres;

--
-- Name: st_force_3dm(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_force_3dm(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_3dm';


ALTER FUNCTION public.st_force_3dm(geometry) OWNER TO postgres;

--
-- Name: st_force_3dz(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_force_3dz(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_3dz';


ALTER FUNCTION public.st_force_3dz(geometry) OWNER TO postgres;

--
-- Name: st_force_4d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_force_4d(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_4d';


ALTER FUNCTION public.st_force_4d(geometry) OWNER TO postgres;

--
-- Name: st_force_collection(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_force_collection(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_collection';


ALTER FUNCTION public.st_force_collection(geometry) OWNER TO postgres;

--
-- Name: st_forcerhr(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_forcerhr(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_clockwise_poly';


ALTER FUNCTION public.st_forcerhr(geometry) OWNER TO postgres;

--
-- Name: st_geogfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geogfromtext(text) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_from_text';


ALTER FUNCTION public.st_geogfromtext(text) OWNER TO postgres;

--
-- Name: st_geogfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geogfromwkb(bytea) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_from_binary';


ALTER FUNCTION public.st_geogfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_geographyfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geographyfromtext(text) RETURNS geography
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geography_from_text';


ALTER FUNCTION public.st_geographyfromtext(text) OWNER TO postgres;

--
-- Name: st_geohash(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geohash(geom geometry, maxchars integer DEFAULT 0) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_GeoHash';


ALTER FUNCTION public.st_geohash(geom geometry, maxchars integer) OWNER TO postgres;

--
-- Name: st_geomcollfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomcollfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE
	WHEN geometrytype(ST_GeomFromText($1)) = 'GEOMETRYCOLLECTION'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_geomcollfromtext(text) OWNER TO postgres;

--
-- Name: st_geomcollfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomcollfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE
	WHEN geometrytype(ST_GeomFromText($1, $2)) = 'GEOMETRYCOLLECTION'
	THEN ST_GeomFromText($1,$2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_geomcollfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_geomcollfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomcollfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE
	WHEN geometrytype(ST_GeomFromWKB($1)) = 'GEOMETRYCOLLECTION'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_geomcollfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_geomcollfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomcollfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE
	WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'GEOMETRYCOLLECTION'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_geomcollfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_geometryfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geometryfromtext(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_text';


ALTER FUNCTION public.st_geometryfromtext(text) OWNER TO postgres;

--
-- Name: st_geometryfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geometryfromtext(text, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_text';


ALTER FUNCTION public.st_geometryfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_geometryn(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geometryn(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_geometryn_collection';


ALTER FUNCTION public.st_geometryn(geometry, integer) OWNER TO postgres;

--
-- Name: st_geometrytype(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geometrytype(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geometry_geometrytype';


ALTER FUNCTION public.st_geometrytype(geometry) OWNER TO postgres;

--
-- Name: st_geomfromewkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromewkb(bytea) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOMFromWKB';


ALTER FUNCTION public.st_geomfromewkb(bytea) OWNER TO postgres;

--
-- Name: st_geomfromewkt(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromewkt(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'parse_WKT_lwgeom';


ALTER FUNCTION public.st_geomfromewkt(text) OWNER TO postgres;

--
-- Name: st_geomfromgeojson(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromgeojson(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geom_from_geojson';


ALTER FUNCTION public.st_geomfromgeojson(text) OWNER TO postgres;

--
-- Name: st_geomfromgml(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromgml(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_GeomFromGML($1, 0)$_$;


ALTER FUNCTION public.st_geomfromgml(text) OWNER TO postgres;

--
-- Name: st_geomfromgml(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromgml(text, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geom_from_gml';


ALTER FUNCTION public.st_geomfromgml(text, integer) OWNER TO postgres;

--
-- Name: st_geomfromkml(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromkml(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geom_from_kml';


ALTER FUNCTION public.st_geomfromkml(text) OWNER TO postgres;

--
-- Name: st_geomfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromtext(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_text';


ALTER FUNCTION public.st_geomfromtext(text) OWNER TO postgres;

--
-- Name: st_geomfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromtext(text, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_text';


ALTER FUNCTION public.st_geomfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_geomfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromwkb(bytea) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_WKB';


ALTER FUNCTION public.st_geomfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_geomfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_geomfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_SetSRID(ST_GeomFromWKB($1), $2)$_$;


ALTER FUNCTION public.st_geomfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_gmltosql(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_gmltosql(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_GeomFromGML($1, 0)$_$;


ALTER FUNCTION public.st_gmltosql(text) OWNER TO postgres;

--
-- Name: st_gmltosql(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_gmltosql(text, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geom_from_gml';


ALTER FUNCTION public.st_gmltosql(text, integer) OWNER TO postgres;

--
-- Name: st_hasarc(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_hasarc(geometry geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_has_arc';


ALTER FUNCTION public.st_hasarc(geometry geometry) OWNER TO postgres;

--
-- Name: st_hausdorffdistance(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_hausdorffdistance(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'hausdorffdistance';


ALTER FUNCTION public.st_hausdorffdistance(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_hausdorffdistance(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_hausdorffdistance(geom1 geometry, geom2 geometry, double precision) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'hausdorffdistancedensify';


ALTER FUNCTION public.st_hausdorffdistance(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: st_interiorringn(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_interiorringn(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_interiorringn_polygon';


ALTER FUNCTION public.st_interiorringn(geometry, integer) OWNER TO postgres;

--
-- Name: st_interpolatepoint(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_interpolatepoint(line geometry, point geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_InterpolatePoint';


ALTER FUNCTION public.st_interpolatepoint(line geometry, point geometry) OWNER TO postgres;

--
-- Name: st_intersection(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_intersection(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'intersection';


ALTER FUNCTION public.st_intersection(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_intersection(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_intersection(geography, geography) RETURNS geography
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT geography(ST_Transform(ST_Intersection(ST_Transform(geometry($1), _ST_BestSRID($1, $2)), ST_Transform(geometry($2), _ST_BestSRID($1, $2))), 4326))$_$;


ALTER FUNCTION public.st_intersection(geography, geography) OWNER TO postgres;

--
-- Name: st_intersection(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_intersection(text, text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_Intersection($1::geometry, $2::geometry);  $_$;


ALTER FUNCTION public.st_intersection(text, text) OWNER TO postgres;

--
-- Name: st_intersects(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_intersects(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Intersects($1,$2)$_$;


ALTER FUNCTION public.st_intersects(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_intersects(geography, geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_intersects(geography, geography) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Distance($1, $2, 0.0, false) < 0.00001$_$;


ALTER FUNCTION public.st_intersects(geography, geography) OWNER TO postgres;

--
-- Name: st_intersects(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_intersects(text, text) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT ST_Intersects($1::geometry, $2::geometry);  $_$;


ALTER FUNCTION public.st_intersects(text, text) OWNER TO postgres;

--
-- Name: st_isclosed(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isclosed(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_isclosed';


ALTER FUNCTION public.st_isclosed(geometry) OWNER TO postgres;

--
-- Name: st_iscollection(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_iscollection(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_IsCollection';


ALTER FUNCTION public.st_iscollection(geometry) OWNER TO postgres;

--
-- Name: st_isempty(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isempty(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_isempty';


ALTER FUNCTION public.st_isempty(geometry) OWNER TO postgres;

--
-- Name: st_isring(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isring(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'isring';


ALTER FUNCTION public.st_isring(geometry) OWNER TO postgres;

--
-- Name: st_issimple(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_issimple(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'issimple';


ALTER FUNCTION public.st_issimple(geometry) OWNER TO postgres;

--
-- Name: st_isvalid(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isvalid(geometry) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'isvalid';


ALTER FUNCTION public.st_isvalid(geometry) OWNER TO postgres;

--
-- Name: st_isvalid(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isvalid(geometry, integer) RETURNS boolean
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT (ST_isValidDetail($1, $2)).valid$_$;


ALTER FUNCTION public.st_isvalid(geometry, integer) OWNER TO postgres;

--
-- Name: st_isvaliddetail(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isvaliddetail(geometry) RETURNS valid_detail
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'isvaliddetail';


ALTER FUNCTION public.st_isvaliddetail(geometry) OWNER TO postgres;

--
-- Name: st_isvaliddetail(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isvaliddetail(geometry, integer) RETURNS valid_detail
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'isvaliddetail';


ALTER FUNCTION public.st_isvaliddetail(geometry, integer) OWNER TO postgres;

--
-- Name: st_isvalidreason(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isvalidreason(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'isvalidreason';


ALTER FUNCTION public.st_isvalidreason(geometry) OWNER TO postgres;

--
-- Name: st_isvalidreason(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_isvalidreason(geometry, integer) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT CASE WHEN valid THEN 'Valid Geometry' ELSE reason END FROM (
	SELECT (ST_isValidDetail($1, $2)).*
) foo
	$_$;


ALTER FUNCTION public.st_isvalidreason(geometry, integer) OWNER TO postgres;

--
-- Name: st_length(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_length(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_length2d_linestring';


ALTER FUNCTION public.st_length(geometry) OWNER TO postgres;

--
-- Name: st_length(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_length(text) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_Length($1::geometry);  $_$;


ALTER FUNCTION public.st_length(text) OWNER TO postgres;

--
-- Name: st_length(geography, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_length(geog geography, use_spheroid boolean DEFAULT true) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_length';


ALTER FUNCTION public.st_length(geog geography, use_spheroid boolean) OWNER TO postgres;

--
-- Name: st_length2d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_length2d(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_length2d_linestring';


ALTER FUNCTION public.st_length2d(geometry) OWNER TO postgres;

--
-- Name: st_length2d_spheroid(geometry, spheroid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_length2d_spheroid(geometry, spheroid) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_length2d_ellipsoid';


ALTER FUNCTION public.st_length2d_spheroid(geometry, spheroid) OWNER TO postgres;

--
-- Name: st_length_spheroid(geometry, spheroid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_length_spheroid(geometry, spheroid) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'LWGEOM_length_ellipsoid_linestring';


ALTER FUNCTION public.st_length_spheroid(geometry, spheroid) OWNER TO postgres;

--
-- Name: st_line_interpolate_point(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_line_interpolate_point(geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_line_interpolate_point';


ALTER FUNCTION public.st_line_interpolate_point(geometry, double precision) OWNER TO postgres;

--
-- Name: st_line_locate_point(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_line_locate_point(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_line_locate_point';


ALTER FUNCTION public.st_line_locate_point(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_line_substring(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_line_substring(geometry, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_line_substring';


ALTER FUNCTION public.st_line_substring(geometry, double precision, double precision) OWNER TO postgres;

--
-- Name: st_linecrossingdirection(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linecrossingdirection(geom1 geometry, geom2 geometry) RETURNS integer
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT CASE WHEN NOT $1 && $2 THEN 0 ELSE _ST_LineCrossingDirection($1,$2) END $_$;


ALTER FUNCTION public.st_linecrossingdirection(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_linefrommultipoint(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linefrommultipoint(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_line_from_mpoint';


ALTER FUNCTION public.st_linefrommultipoint(geometry) OWNER TO postgres;

--
-- Name: st_linefromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linefromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1)) = 'LINESTRING'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_linefromtext(text) OWNER TO postgres;

--
-- Name: st_linefromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linefromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1, $2)) = 'LINESTRING'
	THEN ST_GeomFromText($1,$2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_linefromtext(text, integer) OWNER TO postgres;

--
-- Name: st_linefromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linefromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'LINESTRING'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_linefromwkb(bytea) OWNER TO postgres;

--
-- Name: st_linefromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linefromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'LINESTRING'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_linefromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_linemerge(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linemerge(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'linemerge';


ALTER FUNCTION public.st_linemerge(geometry) OWNER TO postgres;

--
-- Name: st_linestringfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linestringfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'LINESTRING'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_linestringfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_linestringfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linestringfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'LINESTRING'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_linestringfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_linetocurve(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_linetocurve(geometry geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_line_desegmentize';


ALTER FUNCTION public.st_linetocurve(geometry geometry) OWNER TO postgres;

--
-- Name: st_locate_along_measure(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_locate_along_measure(geometry, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ SELECT ST_locate_between_measures($1, $2, $2) $_$;


ALTER FUNCTION public.st_locate_along_measure(geometry, double precision) OWNER TO postgres;

--
-- Name: st_locate_between_measures(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_locate_between_measures(geometry, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_locate_between_m';


ALTER FUNCTION public.st_locate_between_measures(geometry, double precision, double precision) OWNER TO postgres;

--
-- Name: st_locatealong(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_locatealong(geometry geometry, measure double precision, leftrightoffset double precision DEFAULT 0.0) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_LocateAlong';


ALTER FUNCTION public.st_locatealong(geometry geometry, measure double precision, leftrightoffset double precision) OWNER TO postgres;

--
-- Name: st_locatebetween(geometry, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_locatebetween(geometry geometry, frommeasure double precision, tomeasure double precision, leftrightoffset double precision DEFAULT 0.0) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_LocateBetween';


ALTER FUNCTION public.st_locatebetween(geometry geometry, frommeasure double precision, tomeasure double precision, leftrightoffset double precision) OWNER TO postgres;

--
-- Name: st_locatebetweenelevations(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_locatebetweenelevations(geometry geometry, fromelevation double precision, toelevation double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_LocateBetweenElevations';


ALTER FUNCTION public.st_locatebetweenelevations(geometry geometry, fromelevation double precision, toelevation double precision) OWNER TO postgres;

--
-- Name: st_longestline(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_longestline(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_LongestLine(ST_ConvexHull($1), ST_ConvexHull($2))$_$;


ALTER FUNCTION public.st_longestline(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_m(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_m(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_m_point';


ALTER FUNCTION public.st_m(geometry) OWNER TO postgres;

--
-- Name: st_makebox2d(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makebox2d(geom1 geometry, geom2 geometry) RETURNS box2d
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX2D_construct';


ALTER FUNCTION public.st_makebox2d(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_makeenvelope(double precision, double precision, double precision, double precision, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makeenvelope(double precision, double precision, double precision, double precision, integer DEFAULT 0) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_MakeEnvelope';


ALTER FUNCTION public.st_makeenvelope(double precision, double precision, double precision, double precision, integer) OWNER TO postgres;

--
-- Name: st_makeline(geometry[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makeline(geometry[]) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makeline_garray';


ALTER FUNCTION public.st_makeline(geometry[]) OWNER TO postgres;

--
-- Name: st_makeline(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makeline(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makeline';


ALTER FUNCTION public.st_makeline(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_makepoint(double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makepoint(double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoint';


ALTER FUNCTION public.st_makepoint(double precision, double precision) OWNER TO postgres;

--
-- Name: st_makepoint(double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makepoint(double precision, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoint';


ALTER FUNCTION public.st_makepoint(double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_makepoint(double precision, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makepoint(double precision, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoint';


ALTER FUNCTION public.st_makepoint(double precision, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_makepointm(double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makepointm(double precision, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoint3dm';


ALTER FUNCTION public.st_makepointm(double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_makepolygon(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makepolygon(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoly';


ALTER FUNCTION public.st_makepolygon(geometry) OWNER TO postgres;

--
-- Name: st_makepolygon(geometry, geometry[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makepolygon(geometry, geometry[]) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoly';


ALTER FUNCTION public.st_makepolygon(geometry, geometry[]) OWNER TO postgres;

--
-- Name: st_makevalid(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_makevalid(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_MakeValid';


ALTER FUNCTION public.st_makevalid(geometry) OWNER TO postgres;

--
-- Name: st_maxdistance(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_maxdistance(geom1 geometry, geom2 geometry) RETURNS double precision
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT _ST_MaxDistance(ST_ConvexHull($1), ST_ConvexHull($2))$_$;


ALTER FUNCTION public.st_maxdistance(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_mem_size(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mem_size(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_mem_size';


ALTER FUNCTION public.st_mem_size(geometry) OWNER TO postgres;

--
-- Name: st_minimumboundingcircle(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_minimumboundingcircle(inputgeom geometry, segs_per_quarter integer DEFAULT 48) RETURNS geometry
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
	DECLARE
	hull GEOMETRY;
	ring GEOMETRY;
	center GEOMETRY;
	radius DOUBLE PRECISION;
	dist DOUBLE PRECISION;
	d DOUBLE PRECISION;
	idx1 integer;
	idx2 integer;
	l1 GEOMETRY;
	l2 GEOMETRY;
	p1 GEOMETRY;
	p2 GEOMETRY;
	a1 DOUBLE PRECISION;
	a2 DOUBLE PRECISION;


	BEGIN

	-- First compute the ConvexHull of the geometry
	hull = ST_ConvexHull(inputgeom);
	--A point really has no MBC
	IF ST_GeometryType(hull) = 'ST_Point' THEN
		RETURN hull;
	END IF;
	-- convert the hull perimeter to a linestring so we can manipulate individual points
	--If its already a linestring force it to a closed linestring
	ring = CASE WHEN ST_GeometryType(hull) = 'ST_LineString' THEN ST_AddPoint(hull, ST_StartPoint(hull)) ELSE ST_ExteriorRing(hull) END;

	dist = 0;
	-- Brute Force - check every pair
	FOR i in 1 .. (ST_NumPoints(ring)-2)
		LOOP
			FOR j in i .. (ST_NumPoints(ring)-1)
				LOOP
				d = ST_Distance(ST_PointN(ring,i),ST_PointN(ring,j));
				-- Check the distance and update if larger
				IF (d > dist) THEN
					dist = d;
					idx1 = i;
					idx2 = j;
				END IF;
			END LOOP;
		END LOOP;

	-- We now have the diameter of the convex hull.  The following line returns it if desired.
	-- RETURN ST_MakeLine(ST_PointN(ring,idx1),ST_PointN(ring,idx2));

	-- Now for the Minimum Bounding Circle.  Since we know the two points furthest from each
	-- other, the MBC must go through those two points. Start with those points as a diameter of a circle.

	-- The radius is half the distance between them and the center is midway between them
	radius = ST_Distance(ST_PointN(ring,idx1),ST_PointN(ring,idx2)) / 2.0;
	center = ST_Line_interpolate_point(ST_MakeLine(ST_PointN(ring,idx1),ST_PointN(ring,idx2)),0.5);

	-- Loop through each vertex and check if the distance from the center to the point
	-- is greater than the current radius.
	FOR k in 1 .. (ST_NumPoints(ring)-1)
		LOOP
		IF(k <> idx1 and k <> idx2) THEN
			dist = ST_Distance(center,ST_PointN(ring,k));
			IF (dist > radius) THEN
				-- We have to expand the circle.  The new circle must pass trhough
				-- three points - the two original diameters and this point.

				-- Draw a line from the first diameter to this point
				l1 = ST_Makeline(ST_PointN(ring,idx1),ST_PointN(ring,k));
				-- Compute the midpoint
				p1 = ST_line_interpolate_point(l1,0.5);
				-- Rotate the line 90 degrees around the midpoint (perpendicular bisector)
				l1 = ST_Rotate(l1,pi()/2,p1);
				--  Compute the azimuth of the bisector
				a1 = ST_Azimuth(ST_PointN(l1,1),ST_PointN(l1,2));
				--  Extend the line in each direction the new computed distance to insure they will intersect
				l1 = ST_AddPoint(l1,ST_Makepoint(ST_X(ST_PointN(l1,2))+sin(a1)*dist,ST_Y(ST_PointN(l1,2))+cos(a1)*dist),-1);
				l1 = ST_AddPoint(l1,ST_Makepoint(ST_X(ST_PointN(l1,1))-sin(a1)*dist,ST_Y(ST_PointN(l1,1))-cos(a1)*dist),0);

				-- Repeat for the line from the point to the other diameter point
				l2 = ST_Makeline(ST_PointN(ring,idx2),ST_PointN(ring,k));
				p2 = ST_Line_interpolate_point(l2,0.5);
				l2 = ST_Rotate(l2,pi()/2,p2);
				a2 = ST_Azimuth(ST_PointN(l2,1),ST_PointN(l2,2));
				l2 = ST_AddPoint(l2,ST_Makepoint(ST_X(ST_PointN(l2,2))+sin(a2)*dist,ST_Y(ST_PointN(l2,2))+cos(a2)*dist),-1);
				l2 = ST_AddPoint(l2,ST_Makepoint(ST_X(ST_PointN(l2,1))-sin(a2)*dist,ST_Y(ST_PointN(l2,1))-cos(a2)*dist),0);

				-- The new center is the intersection of the two bisectors
				center = ST_Intersection(l1,l2);
				-- The new radius is the distance to any of the three points
				radius = ST_Distance(center,ST_PointN(ring,idx1));
			END IF;
		END IF;
		END LOOP;
	--DONE!!  Return the MBC via the buffer command
	RETURN ST_Buffer(center,radius,segs_per_quarter);

	END;
$$;


ALTER FUNCTION public.st_minimumboundingcircle(inputgeom geometry, segs_per_quarter integer) OWNER TO postgres;

--
-- Name: st_mlinefromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mlinefromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1)) = 'MULTILINESTRING'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mlinefromtext(text) OWNER TO postgres;

--
-- Name: st_mlinefromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mlinefromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE
	WHEN geometrytype(ST_GeomFromText($1, $2)) = 'MULTILINESTRING'
	THEN ST_GeomFromText($1,$2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mlinefromtext(text, integer) OWNER TO postgres;

--
-- Name: st_mlinefromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mlinefromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'MULTILINESTRING'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mlinefromwkb(bytea) OWNER TO postgres;

--
-- Name: st_mlinefromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mlinefromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'MULTILINESTRING'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mlinefromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_mpointfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpointfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1)) = 'MULTIPOINT'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpointfromtext(text) OWNER TO postgres;

--
-- Name: st_mpointfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpointfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1, $2)) = 'MULTIPOINT'
	THEN ST_GeomFromText($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpointfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_mpointfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpointfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'MULTIPOINT'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpointfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_mpointfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpointfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'MULTIPOINT'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpointfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_mpolyfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpolyfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1)) = 'MULTIPOLYGON'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpolyfromtext(text) OWNER TO postgres;

--
-- Name: st_mpolyfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpolyfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1, $2)) = 'MULTIPOLYGON'
	THEN ST_GeomFromText($1,$2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpolyfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_mpolyfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpolyfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'MULTIPOLYGON'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpolyfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_mpolyfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_mpolyfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'MULTIPOLYGON'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_mpolyfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_multi(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multi(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_force_multi';


ALTER FUNCTION public.st_multi(geometry) OWNER TO postgres;

--
-- Name: st_multilinefromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multilinefromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'MULTILINESTRING'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_multilinefromwkb(bytea) OWNER TO postgres;

--
-- Name: st_multilinestringfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multilinestringfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_MLineFromText($1)$_$;


ALTER FUNCTION public.st_multilinestringfromtext(text) OWNER TO postgres;

--
-- Name: st_multilinestringfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multilinestringfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_MLineFromText($1, $2)$_$;


ALTER FUNCTION public.st_multilinestringfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_multipointfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipointfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_MPointFromText($1)$_$;


ALTER FUNCTION public.st_multipointfromtext(text) OWNER TO postgres;

--
-- Name: st_multipointfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipointfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'MULTIPOINT'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_multipointfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_multipointfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipointfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1,$2)) = 'MULTIPOINT'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_multipointfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_multipolyfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipolyfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'MULTIPOLYGON'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_multipolyfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_multipolyfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipolyfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'MULTIPOLYGON'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_multipolyfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_multipolygonfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipolygonfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_MPolyFromText($1)$_$;


ALTER FUNCTION public.st_multipolygonfromtext(text) OWNER TO postgres;

--
-- Name: st_multipolygonfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_multipolygonfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_MPolyFromText($1, $2)$_$;


ALTER FUNCTION public.st_multipolygonfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_ndims(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_ndims(geometry) RETURNS smallint
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_ndims';


ALTER FUNCTION public.st_ndims(geometry) OWNER TO postgres;

--
-- Name: st_node(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_node(g geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_Node';


ALTER FUNCTION public.st_node(g geometry) OWNER TO postgres;

--
-- Name: st_npoints(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_npoints(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_npoints';


ALTER FUNCTION public.st_npoints(geometry) OWNER TO postgres;

--
-- Name: st_nrings(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_nrings(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_nrings';


ALTER FUNCTION public.st_nrings(geometry) OWNER TO postgres;

--
-- Name: st_numgeometries(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_numgeometries(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_numgeometries_collection';


ALTER FUNCTION public.st_numgeometries(geometry) OWNER TO postgres;

--
-- Name: st_numinteriorring(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_numinteriorring(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_numinteriorrings_polygon';


ALTER FUNCTION public.st_numinteriorring(geometry) OWNER TO postgres;

--
-- Name: st_numinteriorrings(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_numinteriorrings(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_numinteriorrings_polygon';


ALTER FUNCTION public.st_numinteriorrings(geometry) OWNER TO postgres;

--
-- Name: st_numpatches(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_numpatches(geometry) RETURNS integer
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN ST_GeometryType($1) = 'ST_PolyhedralSurface'
	THEN ST_NumGeometries($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_numpatches(geometry) OWNER TO postgres;

--
-- Name: st_numpoints(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_numpoints(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_numpoints_linestring';


ALTER FUNCTION public.st_numpoints(geometry) OWNER TO postgres;

--
-- Name: st_offsetcurve(geometry, double precision, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_offsetcurve(line geometry, distance double precision, params text DEFAULT ''::text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_OffsetCurve';


ALTER FUNCTION public.st_offsetcurve(line geometry, distance double precision, params text) OWNER TO postgres;

--
-- Name: st_orderingequals(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_orderingequals(geometrya geometry, geometryb geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ 
	SELECT $1 ~= $2 AND _ST_OrderingEquals($1, $2)
	$_$;


ALTER FUNCTION public.st_orderingequals(geometrya geometry, geometryb geometry) OWNER TO postgres;

--
-- Name: st_overlaps(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_overlaps(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Overlaps($1,$2)$_$;


ALTER FUNCTION public.st_overlaps(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_patchn(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_patchn(geometry, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN ST_GeometryType($1) = 'ST_PolyhedralSurface'
	THEN ST_GeometryN($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_patchn(geometry, integer) OWNER TO postgres;

--
-- Name: st_perimeter(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_perimeter(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_perimeter2d_poly';


ALTER FUNCTION public.st_perimeter(geometry) OWNER TO postgres;

--
-- Name: st_perimeter(geography, boolean); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_perimeter(geog geography, use_spheroid boolean DEFAULT true) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'geography_perimeter';


ALTER FUNCTION public.st_perimeter(geog geography, use_spheroid boolean) OWNER TO postgres;

--
-- Name: st_perimeter2d(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_perimeter2d(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_perimeter2d_poly';


ALTER FUNCTION public.st_perimeter2d(geometry) OWNER TO postgres;

--
-- Name: st_point(double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_point(double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_makepoint';


ALTER FUNCTION public.st_point(double precision, double precision) OWNER TO postgres;

--
-- Name: st_point_inside_circle(geometry, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_point_inside_circle(geometry, double precision, double precision, double precision) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_inside_circle_point';


ALTER FUNCTION public.st_point_inside_circle(geometry, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_pointfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_pointfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1)) = 'POINT'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_pointfromtext(text) OWNER TO postgres;

--
-- Name: st_pointfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_pointfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1, $2)) = 'POINT'
	THEN ST_GeomFromText($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_pointfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_pointfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_pointfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'POINT'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_pointfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_pointfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_pointfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'POINT'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_pointfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_pointn(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_pointn(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_pointn_linestring';


ALTER FUNCTION public.st_pointn(geometry, integer) OWNER TO postgres;

--
-- Name: st_pointonsurface(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_pointonsurface(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'pointonsurface';


ALTER FUNCTION public.st_pointonsurface(geometry) OWNER TO postgres;

--
-- Name: st_polyfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polyfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1)) = 'POLYGON'
	THEN ST_GeomFromText($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_polyfromtext(text) OWNER TO postgres;

--
-- Name: st_polyfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polyfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromText($1, $2)) = 'POLYGON'
	THEN ST_GeomFromText($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_polyfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_polyfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polyfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'POLYGON'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_polyfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_polyfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polyfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1, $2)) = 'POLYGON'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_polyfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_polygon(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polygon(geometry, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$ 
	SELECT ST_SetSRID(ST_MakePolygon($1), $2)
	$_$;


ALTER FUNCTION public.st_polygon(geometry, integer) OWNER TO postgres;

--
-- Name: st_polygonfromtext(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polygonfromtext(text) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_PolyFromText($1)$_$;


ALTER FUNCTION public.st_polygonfromtext(text) OWNER TO postgres;

--
-- Name: st_polygonfromtext(text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polygonfromtext(text, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_PolyFromText($1, $2)$_$;


ALTER FUNCTION public.st_polygonfromtext(text, integer) OWNER TO postgres;

--
-- Name: st_polygonfromwkb(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polygonfromwkb(bytea) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1)) = 'POLYGON'
	THEN ST_GeomFromWKB($1)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_polygonfromwkb(bytea) OWNER TO postgres;

--
-- Name: st_polygonfromwkb(bytea, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polygonfromwkb(bytea, integer) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
	SELECT CASE WHEN geometrytype(ST_GeomFromWKB($1,$2)) = 'POLYGON'
	THEN ST_GeomFromWKB($1, $2)
	ELSE NULL END
	$_$;


ALTER FUNCTION public.st_polygonfromwkb(bytea, integer) OWNER TO postgres;

--
-- Name: st_polygonize(geometry[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_polygonize(geometry[]) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'polygonize_garray';


ALTER FUNCTION public.st_polygonize(geometry[]) OWNER TO postgres;

--
-- Name: st_project(geography, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_project(geog geography, distance double precision, azimuth double precision) RETURNS geography
    LANGUAGE c IMMUTABLE COST 100
    AS '$libdir/postgis-2.0', 'geography_project';


ALTER FUNCTION public.st_project(geog geography, distance double precision, azimuth double precision) OWNER TO postgres;

--
-- Name: st_relate(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_relate(geom1 geometry, geom2 geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'relate_full';


ALTER FUNCTION public.st_relate(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_relate(geometry, geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_relate(geom1 geometry, geom2 geometry, integer) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'relate_full';


ALTER FUNCTION public.st_relate(geom1 geometry, geom2 geometry, integer) OWNER TO postgres;

--
-- Name: st_relate(geometry, geometry, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_relate(geom1 geometry, geom2 geometry, text) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'relate_pattern';


ALTER FUNCTION public.st_relate(geom1 geometry, geom2 geometry, text) OWNER TO postgres;

--
-- Name: st_relatematch(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_relatematch(text, text) RETURNS boolean
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_RelateMatch';


ALTER FUNCTION public.st_relatematch(text, text) OWNER TO postgres;

--
-- Name: st_removepoint(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_removepoint(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_removepoint';


ALTER FUNCTION public.st_removepoint(geometry, integer) OWNER TO postgres;

--
-- Name: st_removerepeatedpoints(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_removerepeatedpoints(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_RemoveRepeatedPoints';


ALTER FUNCTION public.st_removerepeatedpoints(geometry) OWNER TO postgres;

--
-- Name: st_reverse(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_reverse(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_reverse';


ALTER FUNCTION public.st_reverse(geometry) OWNER TO postgres;

--
-- Name: st_rotate(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_rotate(geometry, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  cos($2), -sin($2), 0,  sin($2), cos($2), 0,  0, 0, 1,  0, 0, 0)$_$;


ALTER FUNCTION public.st_rotate(geometry, double precision) OWNER TO postgres;

--
-- Name: st_rotate(geometry, double precision, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_rotate(geometry, double precision, geometry) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  cos($2), -sin($2), 0,  sin($2),  cos($2), 0, 0, 0, 1, ST_X($3) - cos($2) * ST_X($3) + sin($2) * ST_Y($3), ST_Y($3) - sin($2) * ST_X($3) - cos($2) * ST_Y($3), 0)$_$;


ALTER FUNCTION public.st_rotate(geometry, double precision, geometry) OWNER TO postgres;

--
-- Name: st_rotate(geometry, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_rotate(geometry, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  cos($2), -sin($2), 0,  sin($2),  cos($2), 0, 0, 0, 1,	$3 - cos($2) * $3 + sin($2) * $4, $4 - sin($2) * $3 - cos($2) * $4, 0)$_$;


ALTER FUNCTION public.st_rotate(geometry, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_rotatex(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_rotatex(geometry, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1, 1, 0, 0, 0, cos($2), -sin($2), 0, sin($2), cos($2), 0, 0, 0)$_$;


ALTER FUNCTION public.st_rotatex(geometry, double precision) OWNER TO postgres;

--
-- Name: st_rotatey(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_rotatey(geometry, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  cos($2), 0, sin($2),  0, 1, 0,  -sin($2), 0, cos($2), 0,  0, 0)$_$;


ALTER FUNCTION public.st_rotatey(geometry, double precision) OWNER TO postgres;

--
-- Name: st_rotatez(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_rotatez(geometry, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Rotate($1, $2)$_$;


ALTER FUNCTION public.st_rotatez(geometry, double precision) OWNER TO postgres;

--
-- Name: st_scale(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_scale(geometry, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Scale($1, $2, $3, 1)$_$;


ALTER FUNCTION public.st_scale(geometry, double precision, double precision) OWNER TO postgres;

--
-- Name: st_scale(geometry, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_scale(geometry, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  $2, 0, 0,  0, $3, 0,  0, 0, $4,  0, 0, 0)$_$;


ALTER FUNCTION public.st_scale(geometry, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_segmentize(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_segmentize(geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_segmentize2d';


ALTER FUNCTION public.st_segmentize(geometry, double precision) OWNER TO postgres;

--
-- Name: st_setpoint(geometry, integer, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_setpoint(geometry, integer, geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_setpoint_linestring';


ALTER FUNCTION public.st_setpoint(geometry, integer, geometry) OWNER TO postgres;

--
-- Name: st_setsrid(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_setsrid(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_set_srid';


ALTER FUNCTION public.st_setsrid(geometry, integer) OWNER TO postgres;

--
-- Name: st_sharedpaths(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_sharedpaths(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_SharedPaths';


ALTER FUNCTION public.st_sharedpaths(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_shift_longitude(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_shift_longitude(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_longitude_shift';


ALTER FUNCTION public.st_shift_longitude(geometry) OWNER TO postgres;

--
-- Name: st_shortestline(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_shortestline(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_shortestline2d';


ALTER FUNCTION public.st_shortestline(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_simplify(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_simplify(geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_simplify2d';


ALTER FUNCTION public.st_simplify(geometry, double precision) OWNER TO postgres;

--
-- Name: st_simplifypreservetopology(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_simplifypreservetopology(geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'topologypreservesimplify';


ALTER FUNCTION public.st_simplifypreservetopology(geometry, double precision) OWNER TO postgres;

--
-- Name: st_snap(geometry, geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_snap(geom1 geometry, geom2 geometry, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_Snap';


ALTER FUNCTION public.st_snap(geom1 geometry, geom2 geometry, double precision) OWNER TO postgres;

--
-- Name: st_snaptogrid(geometry, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_snaptogrid(geometry, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_SnapToGrid($1, 0, 0, $2, $2)$_$;


ALTER FUNCTION public.st_snaptogrid(geometry, double precision) OWNER TO postgres;

--
-- Name: st_snaptogrid(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_snaptogrid(geometry, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_SnapToGrid($1, 0, 0, $2, $3)$_$;


ALTER FUNCTION public.st_snaptogrid(geometry, double precision, double precision) OWNER TO postgres;

--
-- Name: st_snaptogrid(geometry, double precision, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_snaptogrid(geometry, double precision, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_snaptogrid';


ALTER FUNCTION public.st_snaptogrid(geometry, double precision, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_snaptogrid(geometry, geometry, double precision, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_snaptogrid(geom1 geometry, geom2 geometry, double precision, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_snaptogrid_pointoff';


ALTER FUNCTION public.st_snaptogrid(geom1 geometry, geom2 geometry, double precision, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_split(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_split(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT COST 100
    AS '$libdir/postgis-2.0', 'ST_Split';


ALTER FUNCTION public.st_split(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_srid(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_srid(geometry) RETURNS integer
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_get_srid';


ALTER FUNCTION public.st_srid(geometry) OWNER TO postgres;

--
-- Name: st_startpoint(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_startpoint(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_startpoint_linestring';


ALTER FUNCTION public.st_startpoint(geometry) OWNER TO postgres;

--
-- Name: st_summary(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_summary(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_summary';


ALTER FUNCTION public.st_summary(geometry) OWNER TO postgres;

--
-- Name: st_summary(geography); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_summary(geography) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_summary';


ALTER FUNCTION public.st_summary(geography) OWNER TO postgres;

--
-- Name: st_symdifference(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_symdifference(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'symdifference';


ALTER FUNCTION public.st_symdifference(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_symmetricdifference(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_symmetricdifference(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'symdifference';


ALTER FUNCTION public.st_symmetricdifference(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_touches(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_touches(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Touches($1,$2)$_$;


ALTER FUNCTION public.st_touches(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_transform(geometry, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_transform(geometry, integer) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'transform';


ALTER FUNCTION public.st_transform(geometry, integer) OWNER TO postgres;

--
-- Name: st_translate(geometry, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_translate(geometry, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Translate($1, $2, $3, 0)$_$;


ALTER FUNCTION public.st_translate(geometry, double precision, double precision) OWNER TO postgres;

--
-- Name: st_translate(geometry, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_translate(geometry, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1, 1, 0, 0, 0, 1, 0, 0, 0, 1, $2, $3, $4)$_$;


ALTER FUNCTION public.st_translate(geometry, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_transscale(geometry, double precision, double precision, double precision, double precision); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_transscale(geometry, double precision, double precision, double precision, double precision) RETURNS geometry
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$SELECT ST_Affine($1,  $4, 0, 0,  0, $5, 0,
		0, 0, 1,  $2 * $4, $3 * $5, 0)$_$;


ALTER FUNCTION public.st_transscale(geometry, double precision, double precision, double precision, double precision) OWNER TO postgres;

--
-- Name: st_unaryunion(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_unaryunion(geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'ST_UnaryUnion';


ALTER FUNCTION public.st_unaryunion(geometry) OWNER TO postgres;

--
-- Name: st_union(geometry[]); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_union(geometry[]) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'pgis_union_geometry_array';


ALTER FUNCTION public.st_union(geometry[]) OWNER TO postgres;

--
-- Name: st_union(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_union(geom1 geometry, geom2 geometry) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'geomunion';


ALTER FUNCTION public.st_union(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_within(geometry, geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_within(geom1 geometry, geom2 geometry) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT $1 && $2 AND _ST_Contains($2,$1)$_$;


ALTER FUNCTION public.st_within(geom1 geometry, geom2 geometry) OWNER TO postgres;

--
-- Name: st_wkbtosql(bytea); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_wkbtosql(wkb bytea) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_WKB';


ALTER FUNCTION public.st_wkbtosql(wkb bytea) OWNER TO postgres;

--
-- Name: st_wkttosql(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_wkttosql(text) RETURNS geometry
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_from_text';


ALTER FUNCTION public.st_wkttosql(text) OWNER TO postgres;

--
-- Name: st_x(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_x(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_x_point';


ALTER FUNCTION public.st_x(geometry) OWNER TO postgres;

--
-- Name: st_xmax(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_xmax(box3d) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_xmax';


ALTER FUNCTION public.st_xmax(box3d) OWNER TO postgres;

--
-- Name: st_xmin(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_xmin(box3d) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_xmin';


ALTER FUNCTION public.st_xmin(box3d) OWNER TO postgres;

--
-- Name: st_y(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_y(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_y_point';


ALTER FUNCTION public.st_y(geometry) OWNER TO postgres;

--
-- Name: st_ymax(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_ymax(box3d) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_ymax';


ALTER FUNCTION public.st_ymax(box3d) OWNER TO postgres;

--
-- Name: st_ymin(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_ymin(box3d) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_ymin';


ALTER FUNCTION public.st_ymin(box3d) OWNER TO postgres;

--
-- Name: st_z(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_z(geometry) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_z_point';


ALTER FUNCTION public.st_z(geometry) OWNER TO postgres;

--
-- Name: st_zmax(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_zmax(box3d) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_zmax';


ALTER FUNCTION public.st_zmax(box3d) OWNER TO postgres;

--
-- Name: st_zmflag(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_zmflag(geometry) RETURNS smallint
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_zmflag';


ALTER FUNCTION public.st_zmflag(geometry) OWNER TO postgres;

--
-- Name: st_zmin(box3d); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION st_zmin(box3d) RETURNS double precision
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'BOX3D_zmin';


ALTER FUNCTION public.st_zmin(box3d) OWNER TO postgres;

--
-- Name: text(geometry); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION text(geometry) RETURNS text
    LANGUAGE c IMMUTABLE STRICT
    AS '$libdir/postgis-2.0', 'LWGEOM_to_text';


ALTER FUNCTION public.text(geometry) OWNER TO postgres;

--
-- Name: unlockrows(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION unlockrows(text) RETURNS integer
    LANGUAGE plpgsql STRICT
    AS $_$ 
DECLARE
	ret int;
BEGIN

	IF NOT LongTransactionsEnabled() THEN
		RAISE EXCEPTION 'Long transaction support disabled, use EnableLongTransaction() to enable.';
	END IF;

	EXECUTE 'DELETE FROM authorization_table where authid = ' ||
		quote_literal($1);

	GET DIAGNOSTICS ret = ROW_COUNT;

	RETURN ret;
END;
$_$;


ALTER FUNCTION public.unlockrows(text) OWNER TO postgres;

--
-- Name: updategeometrysrid(character varying, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION updategeometrysrid(character varying, character varying, integer) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $_$
DECLARE
	ret  text;
BEGIN
	SELECT UpdateGeometrySRID('','',$1,$2,$3) into ret;
	RETURN ret;
END;
$_$;


ALTER FUNCTION public.updategeometrysrid(character varying, character varying, integer) OWNER TO postgres;

--
-- Name: updategeometrysrid(character varying, character varying, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION updategeometrysrid(character varying, character varying, character varying, integer) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $_$
DECLARE
	ret  text;
BEGIN
	SELECT UpdateGeometrySRID('',$1,$2,$3,$4) into ret;
	RETURN ret;
END;
$_$;


ALTER FUNCTION public.updategeometrysrid(character varying, character varying, character varying, integer) OWNER TO postgres;

--
-- Name: updategeometrysrid(character varying, character varying, character varying, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION updategeometrysrid(catalogn_name character varying, schema_name character varying, table_name character varying, column_name character varying, new_srid_in integer) RETURNS text
    LANGUAGE plpgsql STRICT
    AS $$
DECLARE
	myrec RECORD;
	okay boolean;
	cname varchar;
	real_schema name;
	unknown_srid integer;
	new_srid integer := new_srid_in;

BEGIN


	-- Find, check or fix schema_name
	IF ( schema_name != '' ) THEN
		okay = false;

		FOR myrec IN SELECT nspname FROM pg_namespace WHERE text(nspname) = schema_name LOOP
			okay := true;
		END LOOP;

		IF ( okay <> true ) THEN
			RAISE EXCEPTION 'Invalid schema name';
		ELSE
			real_schema = schema_name;
		END IF;
	ELSE
		SELECT INTO real_schema current_schema()::text;
	END IF;

	-- Ensure that column_name is in geometry_columns
	okay = false;
	FOR myrec IN SELECT type, coord_dimension FROM geometry_columns WHERE f_table_schema = text(real_schema) and f_table_name = table_name and f_geometry_column = column_name LOOP
		okay := true;
	END LOOP;
	IF (NOT okay) THEN
		RAISE EXCEPTION 'column not found in geometry_columns table';
		RETURN false;
	END IF;

	-- Ensure that new_srid is valid
	IF ( new_srid > 0 ) THEN
		IF ( SELECT count(*) = 0 from spatial_ref_sys where srid = new_srid ) THEN
			RAISE EXCEPTION 'invalid SRID: % not found in spatial_ref_sys', new_srid;
			RETURN false;
		END IF;
	ELSE
		unknown_srid := ST_SRID('POINT EMPTY'::geometry);
		IF ( new_srid != unknown_srid ) THEN
			new_srid := unknown_srid;
			RAISE NOTICE 'SRID value % converted to the officially unknown SRID value %', new_srid_in, new_srid;
		END IF;
	END IF;

	IF postgis_constraint_srid(schema_name, table_name, column_name) IS NOT NULL THEN 
	-- srid was enforced with constraints before, keep it that way.
        -- Make up constraint name
        cname = 'enforce_srid_'  || column_name;
    
        -- Drop enforce_srid constraint
        EXECUTE 'ALTER TABLE ' || quote_ident(real_schema) ||
            '.' || quote_ident(table_name) ||
            ' DROP constraint ' || quote_ident(cname);
    
        -- Update geometries SRID
        EXECUTE 'UPDATE ' || quote_ident(real_schema) ||
            '.' || quote_ident(table_name) ||
            ' SET ' || quote_ident(column_name) ||
            ' = ST_SetSRID(' || quote_ident(column_name) ||
            ', ' || new_srid::text || ')';
            
        -- Reset enforce_srid constraint
        EXECUTE 'ALTER TABLE ' || quote_ident(real_schema) ||
            '.' || quote_ident(table_name) ||
            ' ADD constraint ' || quote_ident(cname) ||
            ' CHECK (st_srid(' || quote_ident(column_name) ||
            ') = ' || new_srid::text || ')';
    ELSE 
        -- We will use typmod to enforce if no srid constraints
        -- We are using postgis_type_name to lookup the new name 
        -- (in case Paul changes his mind and flips geometry_columns to return old upper case name) 
        EXECUTE 'ALTER TABLE ' || quote_ident(real_schema) || '.' || quote_ident(table_name) || 
        ' ALTER COLUMN ' || quote_ident(column_name) || ' TYPE  geometry(' || postgis_type_name(myrec.type, myrec.coord_dimension, true) || ', ' || new_srid::text || ') USING ST_SetSRID(' || quote_ident(column_name) || ',' || new_srid::text || ');' ;
    END IF;

	RETURN real_schema || '.' || table_name || '.' || column_name ||' SRID changed to ' || new_srid::text;

END;
$$;


ALTER FUNCTION public.updategeometrysrid(catalogn_name character varying, schema_name character varying, table_name character varying, column_name character varying, new_srid_in integer) OWNER TO postgres;

--
-- Name: st_3dextent(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_3dextent(geometry) (
    SFUNC = public.st_combine_bbox,
    STYPE = box3d
);


ALTER AGGREGATE public.st_3dextent(geometry) OWNER TO postgres;

--
-- Name: st_accum(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_accum(geometry) (
    SFUNC = pgis_geometry_accum_transfn,
    STYPE = pgis_abs,
    FINALFUNC = pgis_geometry_accum_finalfn
);


ALTER AGGREGATE public.st_accum(geometry) OWNER TO postgres;

--
-- Name: st_collect(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_collect(geometry) (
    SFUNC = pgis_geometry_accum_transfn,
    STYPE = pgis_abs,
    FINALFUNC = pgis_geometry_collect_finalfn
);


ALTER AGGREGATE public.st_collect(geometry) OWNER TO postgres;

--
-- Name: st_extent(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_extent(geometry) (
    SFUNC = public.st_combine_bbox,
    STYPE = box3d,
    FINALFUNC = public.box2d
);


ALTER AGGREGATE public.st_extent(geometry) OWNER TO postgres;

--
-- Name: st_makeline(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_makeline(geometry) (
    SFUNC = pgis_geometry_accum_transfn,
    STYPE = pgis_abs,
    FINALFUNC = pgis_geometry_makeline_finalfn
);


ALTER AGGREGATE public.st_makeline(geometry) OWNER TO postgres;

--
-- Name: st_memcollect(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_memcollect(geometry) (
    SFUNC = public.st_collect,
    STYPE = geometry
);


ALTER AGGREGATE public.st_memcollect(geometry) OWNER TO postgres;

--
-- Name: st_memunion(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_memunion(geometry) (
    SFUNC = public.st_union,
    STYPE = geometry
);


ALTER AGGREGATE public.st_memunion(geometry) OWNER TO postgres;

--
-- Name: st_polygonize(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_polygonize(geometry) (
    SFUNC = pgis_geometry_accum_transfn,
    STYPE = pgis_abs,
    FINALFUNC = pgis_geometry_polygonize_finalfn
);


ALTER AGGREGATE public.st_polygonize(geometry) OWNER TO postgres;

--
-- Name: st_union(geometry); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE st_union(geometry) (
    SFUNC = pgis_geometry_accum_transfn,
    STYPE = pgis_abs,
    FINALFUNC = pgis_geometry_union_finalfn
);


ALTER AGGREGATE public.st_union(geometry) OWNER TO postgres;

--
-- Name: &&; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR && (
    PROCEDURE = geometry_overlaps,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = &&,
    RESTRICT = geometry_gist_sel_2d,
    JOIN = geometry_gist_joinsel_2d
);


ALTER OPERATOR public.&& (geometry, geometry) OWNER TO postgres;

--
-- Name: &&; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR && (
    PROCEDURE = geography_overlaps,
    LEFTARG = geography,
    RIGHTARG = geography,
    COMMUTATOR = &&,
    RESTRICT = geography_gist_selectivity,
    JOIN = geography_gist_join_selectivity
);


ALTER OPERATOR public.&& (geography, geography) OWNER TO postgres;

--
-- Name: &&&; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR &&& (
    PROCEDURE = geometry_overlaps_nd,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = &&&,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.&&& (geometry, geometry) OWNER TO postgres;

--
-- Name: &<; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR &< (
    PROCEDURE = geometry_overleft,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = &>,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.&< (geometry, geometry) OWNER TO postgres;

--
-- Name: &<|; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR &<| (
    PROCEDURE = geometry_overbelow,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = |&>,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.&<| (geometry, geometry) OWNER TO postgres;

--
-- Name: &>; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR &> (
    PROCEDURE = geometry_overright,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = &<,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.&> (geometry, geometry) OWNER TO postgres;

--
-- Name: <; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR < (
    PROCEDURE = geometry_lt,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = >,
    NEGATOR = >=,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.< (geometry, geometry) OWNER TO postgres;

--
-- Name: <; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR < (
    PROCEDURE = geography_lt,
    LEFTARG = geography,
    RIGHTARG = geography,
    COMMUTATOR = >,
    NEGATOR = >=,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.< (geography, geography) OWNER TO postgres;

--
-- Name: <#>; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR <#> (
    PROCEDURE = geometry_distance_box,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = <#>
);


ALTER OPERATOR public.<#> (geometry, geometry) OWNER TO postgres;

--
-- Name: <->; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR <-> (
    PROCEDURE = geometry_distance_centroid,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = <->
);


ALTER OPERATOR public.<-> (geometry, geometry) OWNER TO postgres;

--
-- Name: <<; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR << (
    PROCEDURE = geometry_left,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = >>,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.<< (geometry, geometry) OWNER TO postgres;

--
-- Name: <<|; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR <<| (
    PROCEDURE = geometry_below,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = |>>,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.<<| (geometry, geometry) OWNER TO postgres;

--
-- Name: <=; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR <= (
    PROCEDURE = geometry_le,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = >=,
    NEGATOR = >,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.<= (geometry, geometry) OWNER TO postgres;

--
-- Name: <=; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR <= (
    PROCEDURE = geography_le,
    LEFTARG = geography,
    RIGHTARG = geography,
    COMMUTATOR = >=,
    NEGATOR = >,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.<= (geography, geography) OWNER TO postgres;

--
-- Name: =; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR = (
    PROCEDURE = geometry_eq,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = =,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.= (geometry, geometry) OWNER TO postgres;

--
-- Name: =; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR = (
    PROCEDURE = geography_eq,
    LEFTARG = geography,
    RIGHTARG = geography,
    COMMUTATOR = =,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.= (geography, geography) OWNER TO postgres;

--
-- Name: >; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR > (
    PROCEDURE = geometry_gt,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = <,
    NEGATOR = <=,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.> (geometry, geometry) OWNER TO postgres;

--
-- Name: >; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR > (
    PROCEDURE = geography_gt,
    LEFTARG = geography,
    RIGHTARG = geography,
    COMMUTATOR = <,
    NEGATOR = <=,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.> (geography, geography) OWNER TO postgres;

--
-- Name: >=; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR >= (
    PROCEDURE = geometry_ge,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = <=,
    NEGATOR = <,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.>= (geometry, geometry) OWNER TO postgres;

--
-- Name: >=; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR >= (
    PROCEDURE = geography_ge,
    LEFTARG = geography,
    RIGHTARG = geography,
    COMMUTATOR = <=,
    NEGATOR = <,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.>= (geography, geography) OWNER TO postgres;

--
-- Name: >>; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR >> (
    PROCEDURE = geometry_right,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = <<,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.>> (geometry, geometry) OWNER TO postgres;

--
-- Name: @; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR @ (
    PROCEDURE = geometry_within,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = ~,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.@ (geometry, geometry) OWNER TO postgres;

--
-- Name: |&>; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR |&> (
    PROCEDURE = geometry_overabove,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = &<|,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.|&> (geometry, geometry) OWNER TO postgres;

--
-- Name: |>>; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR |>> (
    PROCEDURE = geometry_above,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = <<|,
    RESTRICT = positionsel,
    JOIN = positionjoinsel
);


ALTER OPERATOR public.|>> (geometry, geometry) OWNER TO postgres;

--
-- Name: ~; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR ~ (
    PROCEDURE = geometry_contains,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    COMMUTATOR = @,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.~ (geometry, geometry) OWNER TO postgres;

--
-- Name: ~=; Type: OPERATOR; Schema: public; Owner: postgres
--

CREATE OPERATOR ~= (
    PROCEDURE = geometry_same,
    LEFTARG = geometry,
    RIGHTARG = geometry,
    RESTRICT = contsel,
    JOIN = contjoinsel
);


ALTER OPERATOR public.~= (geometry, geometry) OWNER TO postgres;

--
-- Name: btree_geography_ops; Type: OPERATOR CLASS; Schema: public; Owner: postgres
--

CREATE OPERATOR CLASS btree_geography_ops
    DEFAULT FOR TYPE geography USING btree AS
    OPERATOR 1 <(geography,geography) ,
    OPERATOR 2 <=(geography,geography) ,
    OPERATOR 3 =(geography,geography) ,
    OPERATOR 4 >=(geography,geography) ,
    OPERATOR 5 >(geography,geography) ,
    FUNCTION 1 (geography, geography) geography_cmp(geography,geography);


ALTER OPERATOR CLASS public.btree_geography_ops USING btree OWNER TO postgres;

--
-- Name: btree_geometry_ops; Type: OPERATOR CLASS; Schema: public; Owner: postgres
--

CREATE OPERATOR CLASS btree_geometry_ops
    DEFAULT FOR TYPE geometry USING btree AS
    OPERATOR 1 <(geometry,geometry) ,
    OPERATOR 2 <=(geometry,geometry) ,
    OPERATOR 3 =(geometry,geometry) ,
    OPERATOR 4 >=(geometry,geometry) ,
    OPERATOR 5 >(geometry,geometry) ,
    FUNCTION 1 (geometry, geometry) geometry_cmp(geometry,geometry);


ALTER OPERATOR CLASS public.btree_geometry_ops USING btree OWNER TO postgres;

--
-- Name: gist_geography_ops; Type: OPERATOR CLASS; Schema: public; Owner: postgres
--

CREATE OPERATOR CLASS gist_geography_ops
    DEFAULT FOR TYPE geography USING gist AS
    STORAGE gidx ,
    OPERATOR 3 &&(geography,geography) ,
    FUNCTION 1 (geography, geography) geography_gist_consistent(internal,geography,integer) ,
    FUNCTION 2 (geography, geography) geography_gist_union(bytea,internal) ,
    FUNCTION 3 (geography, geography) geography_gist_compress(internal) ,
    FUNCTION 4 (geography, geography) geography_gist_decompress(internal) ,
    FUNCTION 5 (geography, geography) geography_gist_penalty(internal,internal,internal) ,
    FUNCTION 6 (geography, geography) geography_gist_picksplit(internal,internal) ,
    FUNCTION 7 (geography, geography) geography_gist_same(box2d,box2d,internal);


ALTER OPERATOR CLASS public.gist_geography_ops USING gist OWNER TO postgres;

--
-- Name: gist_geometry_ops_2d; Type: OPERATOR CLASS; Schema: public; Owner: postgres
--

CREATE OPERATOR CLASS gist_geometry_ops_2d
    DEFAULT FOR TYPE geometry USING gist AS
    STORAGE box2df ,
    OPERATOR 1 <<(geometry,geometry) ,
    OPERATOR 2 &<(geometry,geometry) ,
    OPERATOR 3 &&(geometry,geometry) ,
    OPERATOR 4 &>(geometry,geometry) ,
    OPERATOR 5 >>(geometry,geometry) ,
    OPERATOR 6 ~=(geometry,geometry) ,
    OPERATOR 7 ~(geometry,geometry) ,
    OPERATOR 8 @(geometry,geometry) ,
    OPERATOR 9 &<|(geometry,geometry) ,
    OPERATOR 10 <<|(geometry,geometry) ,
    OPERATOR 11 |>>(geometry,geometry) ,
    OPERATOR 12 |&>(geometry,geometry) ,
    OPERATOR 13 <->(geometry,geometry) FOR ORDER BY pg_catalog.float_ops ,
    OPERATOR 14 <#>(geometry,geometry) FOR ORDER BY pg_catalog.float_ops ,
    FUNCTION 1 (geometry, geometry) geometry_gist_consistent_2d(internal,geometry,integer) ,
    FUNCTION 2 (geometry, geometry) geometry_gist_union_2d(bytea,internal) ,
    FUNCTION 3 (geometry, geometry) geometry_gist_compress_2d(internal) ,
    FUNCTION 4 (geometry, geometry) geometry_gist_decompress_2d(internal) ,
    FUNCTION 5 (geometry, geometry) geometry_gist_penalty_2d(internal,internal,internal) ,
    FUNCTION 6 (geometry, geometry) geometry_gist_picksplit_2d(internal,internal) ,
    FUNCTION 7 (geometry, geometry) geometry_gist_same_2d(geometry,geometry,internal) ,
    FUNCTION 8 (geometry, geometry) geometry_gist_distance_2d(internal,geometry,integer);


ALTER OPERATOR CLASS public.gist_geometry_ops_2d USING gist OWNER TO postgres;

--
-- Name: gist_geometry_ops_nd; Type: OPERATOR CLASS; Schema: public; Owner: postgres
--

CREATE OPERATOR CLASS gist_geometry_ops_nd
    FOR TYPE geometry USING gist AS
    STORAGE gidx ,
    OPERATOR 3 &&&(geometry,geometry) ,
    FUNCTION 1 (geometry, geometry) geometry_gist_consistent_nd(internal,geometry,integer) ,
    FUNCTION 2 (geometry, geometry) geometry_gist_union_nd(bytea,internal) ,
    FUNCTION 3 (geometry, geometry) geometry_gist_compress_nd(internal) ,
    FUNCTION 4 (geometry, geometry) geometry_gist_decompress_nd(internal) ,
    FUNCTION 5 (geometry, geometry) geometry_gist_penalty_nd(internal,internal,internal) ,
    FUNCTION 6 (geometry, geometry) geometry_gist_picksplit_nd(internal,internal) ,
    FUNCTION 7 (geometry, geometry) geometry_gist_same_nd(geometry,geometry,internal);


ALTER OPERATOR CLASS public.gist_geometry_ops_nd USING gist OWNER TO postgres;

SET search_path = pg_catalog;

--
-- Name: CAST (public.box2d AS public.box3d); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.box2d AS public.box3d) WITH FUNCTION public.box3d(public.box2d) AS IMPLICIT;


--
-- Name: CAST (public.box2d AS public.geometry); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.box2d AS public.geometry) WITH FUNCTION public.geometry(public.box2d) AS IMPLICIT;


--
-- Name: CAST (public.box3d AS box); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.box3d AS box) WITH FUNCTION public.box(public.box3d) AS IMPLICIT;


--
-- Name: CAST (public.box3d AS public.box2d); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.box3d AS public.box2d) WITH FUNCTION public.box2d(public.box3d) AS IMPLICIT;


--
-- Name: CAST (public.box3d AS public.geometry); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.box3d AS public.geometry) WITH FUNCTION public.geometry(public.box3d) AS IMPLICIT;


--
-- Name: CAST (bytea AS public.geography); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (bytea AS public.geography) WITH FUNCTION public.geography(bytea) AS IMPLICIT;


--
-- Name: CAST (bytea AS public.geometry); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (bytea AS public.geometry) WITH FUNCTION public.geometry(bytea) AS IMPLICIT;


--
-- Name: CAST (public.geography AS bytea); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geography AS bytea) WITH FUNCTION public.bytea(public.geography) AS IMPLICIT;


--
-- Name: CAST (public.geography AS public.geography); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geography AS public.geography) WITH FUNCTION public.geography(public.geography, integer, boolean) AS IMPLICIT;


--
-- Name: CAST (public.geography AS public.geometry); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geography AS public.geometry) WITH FUNCTION public.geometry(public.geography);


--
-- Name: CAST (public.geometry AS box); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS box) WITH FUNCTION public.box(public.geometry) AS IMPLICIT;


--
-- Name: CAST (public.geometry AS public.box2d); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS public.box2d) WITH FUNCTION public.box2d(public.geometry) AS IMPLICIT;


--
-- Name: CAST (public.geometry AS public.box3d); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS public.box3d) WITH FUNCTION public.box3d(public.geometry) AS IMPLICIT;


--
-- Name: CAST (public.geometry AS bytea); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS bytea) WITH FUNCTION public.bytea(public.geometry) AS IMPLICIT;


--
-- Name: CAST (public.geometry AS public.geography); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS public.geography) WITH FUNCTION public.geography(public.geometry) AS IMPLICIT;


--
-- Name: CAST (public.geometry AS public.geometry); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS public.geometry) WITH FUNCTION public.geometry(public.geometry, integer, boolean) AS IMPLICIT;


--
-- Name: CAST (public.geometry AS text); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (public.geometry AS text) WITH FUNCTION public.text(public.geometry) AS IMPLICIT;


--
-- Name: CAST (text AS public.geometry); Type: CAST; Schema: pg_catalog; Owner: 
--

CREATE CAST (text AS public.geometry) WITH FUNCTION public.geometry(text) AS IMPLICIT;


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: document; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE document (
    id integer NOT NULL,
    user_id integer NOT NULL,
    path character varying(255) DEFAULT NULL::character varying,
    path2 character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.document OWNER TO postgres;

--
-- Name: document_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE document_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.document_id_seq OWNER TO postgres;

--
-- Name: ext_log_entries; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ext_log_entries (
    id integer NOT NULL,
    action character varying(8) NOT NULL,
    logged_at timestamp(0) without time zone NOT NULL,
    object_id character varying(32) DEFAULT NULL::character varying,
    object_class character varying(255) NOT NULL,
    version integer NOT NULL,
    data text,
    username character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.ext_log_entries OWNER TO postgres;

--
-- Name: COLUMN ext_log_entries.data; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN ext_log_entries.data IS '(DC2Type:array)';


--
-- Name: ext_log_entries_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE ext_log_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ext_log_entries_id_seq OWNER TO postgres;

--
-- Name: ext_translations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ext_translations (
    id integer NOT NULL,
    locale character varying(8) NOT NULL,
    object_class character varying(255) NOT NULL,
    field character varying(32) NOT NULL,
    foreign_key character varying(64) NOT NULL,
    content text
);


ALTER TABLE public.ext_translations OWNER TO postgres;

--
-- Name: ext_translations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE ext_translations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ext_translations_id_seq OWNER TO postgres;

--
-- Name: galleries; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE galleries (
    id integer NOT NULL,
    regular_id integer,
    user_id integer,
    notice_id integer,
    original_id integer
);


ALTER TABLE public.galleries OWNER TO postgres;

--
-- Name: galleries_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE galleries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.galleries_id_seq OWNER TO postgres;

--
-- Name: galleries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE galleries_id_seq OWNED BY galleries.id;


--
-- Name: geography_columns; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW geography_columns AS
    SELECT current_database() AS f_table_catalog, n.nspname AS f_table_schema, c.relname AS f_table_name, a.attname AS f_geography_column, postgis_typmod_dims(a.atttypmod) AS coord_dimension, postgis_typmod_srid(a.atttypmod) AS srid, postgis_typmod_type(a.atttypmod) AS type FROM pg_class c, pg_attribute a, pg_type t, pg_namespace n WHERE (((((((t.typname = 'geography'::name) AND (a.attisdropped = false)) AND (a.atttypid = t.oid)) AND (a.attrelid = c.oid)) AND (c.relnamespace = n.oid)) AND (NOT pg_is_other_temp_schema(c.relnamespace))) AND has_table_privilege(c.oid, 'SELECT'::text));


ALTER TABLE public.geography_columns OWNER TO postgres;

--
-- Name: geometry_columns; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW geometry_columns AS
    SELECT (current_database())::character varying(256) AS f_table_catalog, (n.nspname)::character varying(256) AS f_table_schema, (c.relname)::character varying(256) AS f_table_name, (a.attname)::character varying(256) AS f_geometry_column, COALESCE(NULLIF(postgis_typmod_dims(a.atttypmod), 2), postgis_constraint_dims((n.nspname)::text, (c.relname)::text, (a.attname)::text), 2) AS coord_dimension, COALESCE(NULLIF(postgis_typmod_srid(a.atttypmod), 0), postgis_constraint_srid((n.nspname)::text, (c.relname)::text, (a.attname)::text), 0) AS srid, (replace(replace(COALESCE(NULLIF(upper(postgis_typmod_type(a.atttypmod)), 'GEOMETRY'::text), (postgis_constraint_type((n.nspname)::text, (c.relname)::text, (a.attname)::text))::text, 'GEOMETRY'::text), 'ZM'::text, ''::text), 'Z'::text, ''::text))::character varying(30) AS type FROM pg_class c, pg_attribute a, pg_type t, pg_namespace n WHERE (((((((((t.typname = 'geometry'::name) AND (a.attisdropped = false)) AND (a.atttypid = t.oid)) AND (a.attrelid = c.oid)) AND (c.relnamespace = n.oid)) AND ((c.relkind = 'r'::"char") OR (c.relkind = 'v'::"char"))) AND (NOT pg_is_other_temp_schema(c.relnamespace))) AND (NOT ((n.nspname = 'public'::name) AND (c.relname = 'raster_columns'::name)))) AND has_table_privilege(c.oid, 'SELECT'::text));


ALTER TABLE public.geometry_columns OWNER TO postgres;

--
-- Name: identity__verification; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE identity__verification (
    id integer NOT NULL,
    user_id integer,
    username character varying(255) DEFAULT NULL::character varying,
    status character varying(255) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    activitypoint integer
);


ALTER TABLE public.identity__verification OWNER TO postgres;

--
-- Name: identity__verification_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE identity__verification_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.identity__verification_id_seq OWNER TO postgres;

--
-- Name: identity__verification_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE identity__verification_id_seq OWNED BY identity__verification.id;


--
-- Name: images; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE images (
    id integer NOT NULL,
    gallery_id integer,
    name character varying(255) DEFAULT NULL::character varying,
    sequence integer,
    updated_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    crop boolean
);


ALTER TABLE public.images OWNER TO postgres;

--
-- Name: images_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.images_id_seq OWNER TO postgres;

--
-- Name: images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE images_id_seq OWNED BY images.id;


--
-- Name: location; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE location (
    id integer NOT NULL,
    user_id integer,
    notice_id integer,
    pgisgeog geography(Point,4326) DEFAULT NULL::geography,
    pgisgeom geometry(Point) DEFAULT NULL::geometry,
    location character varying(255) DEFAULT NULL::character varying,
    printable character varying(255) DEFAULT NULL::character varying,
    usergroup_id integer
);


ALTER TABLE public.location OWNER TO postgres;

--
-- Name: COLUMN location.pgisgeog; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN location.pgisgeog IS '(DC2Type:geography)';


--
-- Name: COLUMN location.pgisgeom; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN location.pgisgeom IS '(DC2Type:geometry)';


--
-- Name: location__verification; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE location__verification (
    id integer NOT NULL,
    user_id integer,
    username character varying(255) DEFAULT NULL::character varying,
    password character varying(255) DEFAULT NULL::character varying,
    status character varying(255) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    activitypoint integer,
    lastname character varying(255) DEFAULT NULL::character varying,
    pincode character varying(255) DEFAULT NULL::character varying,
    address text,
    additionaladdress text
);


ALTER TABLE public.location__verification OWNER TO postgres;

--
-- Name: location__verification_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE location__verification_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.location__verification_id_seq OWNER TO postgres;

--
-- Name: location__verification_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE location__verification_id_seq OWNED BY location__verification.id;


--
-- Name: location_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE location_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.location_id_seq OWNER TO postgres;

--
-- Name: location_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE location_id_seq OWNED BY location.id;


--
-- Name: message__about_notice; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE message__about_notice (
    message_id integer NOT NULL,
    about_notice_id integer NOT NULL
);


ALTER TABLE public.message__about_notice OWNER TO postgres;

--
-- Name: message__about_user; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE message__about_user (
    message_id integer NOT NULL,
    about_user_id integer NOT NULL
);


ALTER TABLE public.message__about_user OWNER TO postgres;

--
-- Name: message__messages; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE message__messages (
    id integer NOT NULL,
    sender_user_id integer,
    receiver_user_id integer,
    first_message_id integer,
    prev_message_id integer,
    next_message_id integer,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    read boolean NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    sender_deleted_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    receiver_deleted_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    system boolean NOT NULL,
    usergroup_id integer,
    fromgroup integer
);


ALTER TABLE public.message__messages OWNER TO postgres;

--
-- Name: message__messages_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE message__messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.message__messages_id_seq OWNER TO postgres;

--
-- Name: notice__comments; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__comments (
    id integer NOT NULL,
    author_id integer NOT NULL,
    notice_id integer,
    user_id integer,
    title character varying(255) NOT NULL,
    text character varying(1000) NOT NULL,
    type integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    is_read boolean NOT NULL,
    usergroup_id integer
);


ALTER TABLE public.notice__comments OWNER TO postgres;

--
-- Name: notice__comments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__comments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__comments_id_seq OWNER TO postgres;

--
-- Name: notice__comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__comments_id_seq OWNED BY notice__comments.id;


--
-- Name: notice__dictionary; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__dictionary (
    id integer NOT NULL,
    text character varying(255) NOT NULL,
    tag boolean NOT NULL,
    quantity_search integer NOT NULL,
    quantity_tag integer NOT NULL,
    search_activated boolean NOT NULL,
    tag_activated boolean NOT NULL,
    disabled boolean NOT NULL
);


ALTER TABLE public.notice__dictionary OWNER TO postgres;

--
-- Name: notice__dictionary_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__dictionary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__dictionary_id_seq OWNER TO postgres;

--
-- Name: notice__dictionary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__dictionary_id_seq OWNED BY notice__dictionary.id;


--
-- Name: notice__facebook_feed; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__facebook_feed (
    id integer NOT NULL,
    notice_id integer,
    fbuserid character varying(20) NOT NULL,
    photoid character varying(20) DEFAULT NULL::character varying,
    postid character varying(35) NOT NULL
);


ALTER TABLE public.notice__facebook_feed OWNER TO postgres;

--
-- Name: notice__facebook_feed_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__facebook_feed_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__facebook_feed_id_seq OWNER TO postgres;

--
-- Name: notice__notices; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__notices (
    id integer NOT NULL,
    type_id integer,
    user_id integer NOT NULL,
    title character varying(255) DEFAULT NULL::character varying,
    content text,
    allowed integer,
    draft boolean,
    link character varying(255) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    start_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    end_date timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    slug character varying(255) DEFAULT NULL::character varying,
    on_dashboard boolean,
    tags character varying(255) DEFAULT NULL::character varying,
    date_arrange boolean,
    start_time_arrange boolean,
    location_arrange boolean,
    end_time_arrange boolean,
    end_time time(0) without time zone DEFAULT NULL::timestamp without time zone,
    start_time time(0) without time zone DEFAULT NULL::time without time zone,
    pieces integer,
    one_piece boolean,
    currency character varying(255) DEFAULT NULL::character varying,
    price numeric(10,2) DEFAULT NULL::numeric,
    free boolean,
    unlimited boolean,
    end_date_arrange boolean,
    spot integer,
    default_setting boolean,
    usergroup_id integer,
    completed boolean
);


ALTER TABLE public.notice__notices OWNER TO postgres;

--
-- Name: notice__notices__dictionaries; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__notices__dictionaries (
    dictionary_id integer NOT NULL,
    notice_id integer NOT NULL
);


ALTER TABLE public.notice__notices__dictionaries OWNER TO postgres;

--
-- Name: notice__notices_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__notices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__notices_id_seq OWNER TO postgres;

--
-- Name: notice__notices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__notices_id_seq OWNED BY notice__notices.id;


--
-- Name: notice__property_types; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__property_types (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    element integer NOT NULL,
    options character varying(255) DEFAULT NULL::character varying,
    expanded boolean NOT NULL,
    multiple boolean NOT NULL
);


ALTER TABLE public.notice__property_types OWNER TO postgres;

--
-- Name: notice__property_types_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__property_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__property_types_id_seq OWNER TO postgres;

--
-- Name: notice__property_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__property_types_id_seq OWNED BY notice__property_types.id;


--
-- Name: notice__requests; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__requests (
    id integer NOT NULL,
    author_id integer NOT NULL,
    notice_id integer,
    user_id integer,
    title character varying(255) NOT NULL,
    text character varying(1000) NOT NULL,
    status character varying(255) DEFAULT 'running'::character varying NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    is_read boolean NOT NULL,
    requeststatus character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    piece_spot integer,
    price numeric(10,2) DEFAULT NULL::numeric,
    free boolean,
    proposeprice numeric(10,2) DEFAULT NULL::numeric,
    totalprice numeric(10,2) DEFAULT NULL::numeric,
    currency character varying(255) DEFAULT NULL::character varying,
    is_read_status boolean
);


ALTER TABLE public.notice__requests OWNER TO postgres;

--
-- Name: notice__requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__requests_id_seq OWNER TO postgres;

--
-- Name: notice__requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__requests_id_seq OWNED BY notice__requests.id;


--
-- Name: notice__reviews; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__reviews (
    id integer NOT NULL,
    author_id integer NOT NULL,
    notice_id integer,
    user_id integer,
    text character varying(1000) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    title character varying(255) NOT NULL,
    is_read boolean NOT NULL,
    type integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.notice__reviews OWNER TO postgres;

--
-- Name: notice__reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__reviews_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__reviews_id_seq OWNER TO postgres;

--
-- Name: notice__reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__reviews_id_seq OWNED BY notice__reviews.id;


--
-- Name: notice__type__properties; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__type__properties (
    type_id integer NOT NULL,
    propertytype_id integer NOT NULL
);


ALTER TABLE public.notice__type__properties OWNER TO postgres;

--
-- Name: notice__types; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__types (
    id integer NOT NULL,
    parent_id integer,
    name character varying(255) NOT NULL,
    sequence integer NOT NULL,
    location_change_available boolean NOT NULL,
    price_available boolean,
    spot_available boolean,
    piece_available boolean,
    date_available boolean,
    alttext character varying(255) DEFAULT NULL::character varying,
    orderby integer
);


ALTER TABLE public.notice__types OWNER TO postgres;

--
-- Name: notice__types_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__types_id_seq OWNER TO postgres;

--
-- Name: notice__types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__types_id_seq OWNED BY notice__types.id;


--
-- Name: notice__values; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notice__values (
    id integer NOT NULL,
    notice_id integer,
    property_id integer NOT NULL,
    value character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.notice__values OWNER TO postgres;

--
-- Name: notice__values_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE notice__values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.notice__values_id_seq OWNER TO postgres;

--
-- Name: notice__values_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE notice__values_id_seq OWNED BY notice__values.id;


--
-- Name: payment__setting; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE payment__setting (
    id integer NOT NULL,
    user_id integer,
    account_holder character varying(255) DEFAULT NULL::character varying,
    account_no character varying(255) DEFAULT NULL::character varying,
    bank_code character varying(255) DEFAULT NULL::character varying,
    paypal_email character varying(255) DEFAULT NULL::character varying,
    card_type character varying(255) DEFAULT NULL::character varying,
    card_no character varying(255) DEFAULT NULL::character varying,
    card_holder character varying(255) DEFAULT NULL::character varying,
    end_month character varying(4) DEFAULT NULL::character varying,
    end_year character varying(6) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    agreed boolean,
    type character varying(255) DEFAULT NULL::character varying,
    cvv_code character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.payment__setting OWNER TO postgres;

--
-- Name: payment__setting_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE payment__setting_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payment__setting_id_seq OWNER TO postgres;

--
-- Name: payment__setting_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE payment__setting_id_seq OWNED BY payment__setting.id;


--
-- Name: share__holder; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE share__holder (
    id integer NOT NULL,
    firstname character varying(255) NOT NULL,
    surname character varying(255) NOT NULL,
    city character varying(255) NOT NULL,
    postalcode character varying(255) NOT NULL,
    country character varying(255) NOT NULL,
    birthday date,
    email character varying(255) NOT NULL,
    share integer NOT NULL,
    price numeric(10,2) NOT NULL,
    totalamount numeric(10,2) DEFAULT NULL::numeric,
    terms boolean NOT NULL,
    street character varying(500) DEFAULT NULL::character varying
);


ALTER TABLE public.share__holder OWNER TO postgres;

--
-- Name: share__holder_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE share__holder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.share__holder_id_seq OWNER TO postgres;

--
-- Name: share__holder_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE share__holder_id_seq OWNED BY share__holder.id;


--
-- Name: spatial_ref_sys; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE spatial_ref_sys (
    srid integer NOT NULL,
    auth_name character varying(256),
    auth_srid integer,
    srtext character varying(2048),
    proj4text character varying(2048),
    CONSTRAINT spatial_ref_sys_srid_check CHECK (((srid > 0) AND (srid <= 998999)))
);


ALTER TABLE public.spatial_ref_sys OWNER TO postgres;

--
-- Name: stickers; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE stickers (
    id integer NOT NULL,
    user_id integer,
    notice_id integer,
    review_id integer,
    reported_by_id integer NOT NULL,
    reason text NOT NULL,
    description text,
    created_at timestamp(0) without time zone NOT NULL,
    discarded_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    comment_id integer
);


ALTER TABLE public.stickers OWNER TO postgres;

--
-- Name: stickers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE stickers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stickers_id_seq OWNER TO postgres;

--
-- Name: stickers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE stickers_id_seq OWNED BY stickers.id;


--
-- Name: user__email_change_request; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__email_change_request (
    id integer NOT NULL,
    user_id integer NOT NULL,
    requested_email character varying(90) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    confirmation_token character varying(60) NOT NULL
);


ALTER TABLE public.user__email_change_request OWNER TO postgres;

--
-- Name: user__email_change_request_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__email_change_request_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__email_change_request_id_seq OWNER TO postgres;

--
-- Name: user__friends; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__friends (
    user_regular_id integer NOT NULL,
    friend_user_regular_id integer NOT NULL
);


ALTER TABLE public.user__friends OWNER TO postgres;

--
-- Name: user__friends_invitations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__friends_invitations (
    id integer NOT NULL,
    invitation_by integer,
    invitation_for integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    accepted boolean
);


ALTER TABLE public.user__friends_invitations OWNER TO postgres;

--
-- Name: user__friends_invitations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__friends_invitations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__friends_invitations_id_seq OWNER TO postgres;

--
-- Name: user__group; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__group (
    id integer NOT NULL,
    user_id integer,
    groupname character varying(255) NOT NULL,
    about_group text,
    status integer,
    path character varying(255) DEFAULT NULL::character varying,
    path2 character varying(255) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    locsave integer
);


ALTER TABLE public.user__group OWNER TO postgres;

--
-- Name: user__group_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__group_id_seq OWNER TO postgres;

--
-- Name: user__neighbors; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__neighbors (
    id integer NOT NULL,
    current_id integer NOT NULL,
    neighbor_id integer,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.user__neighbors OWNER TO postgres;

--
-- Name: user__neighbors_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__neighbors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__neighbors_id_seq OWNER TO postgres;

--
-- Name: user__notification_group_intervals; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__notification_group_intervals (
    id integer NOT NULL,
    notification_group_id integer,
    user_id integer,
    "interval" integer NOT NULL
);


ALTER TABLE public.user__notification_group_intervals OWNER TO postgres;

--
-- Name: user__notification_group_intervals_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__notification_group_intervals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__notification_group_intervals_id_seq OWNER TO postgres;

--
-- Name: user__notification_group_intervals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE user__notification_group_intervals_id_seq OWNED BY user__notification_group_intervals.id;


--
-- Name: user__notification_groups; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__notification_groups (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.user__notification_groups OWNER TO postgres;

--
-- Name: user__notification_groups_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__notification_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__notification_groups_id_seq OWNER TO postgres;

--
-- Name: user__notification_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE user__notification_groups_id_seq OWNED BY user__notification_groups.id;


--
-- Name: user__notification_queue; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__notification_queue (
    id integer NOT NULL,
    send_after timestamp(0) without time zone NOT NULL,
    from_address character varying(90) NOT NULL,
    from_name character varying(60) NOT NULL,
    to_address character varying(90) NOT NULL,
    subject character varying(90) NOT NULL,
    body_html text NOT NULL,
    body_plain text NOT NULL
);


ALTER TABLE public.user__notification_queue OWNER TO postgres;

--
-- Name: user__notification_queue_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__notification_queue_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__notification_queue_id_seq OWNER TO postgres;

--
-- Name: user__notifications; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__notifications (
    id integer NOT NULL,
    notification_group_id integer,
    name character varying(255) NOT NULL,
    description character varying(255) NOT NULL
);


ALTER TABLE public.user__notifications OWNER TO postgres;

--
-- Name: user__notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__notifications_id_seq OWNER TO postgres;

--
-- Name: user__notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE user__notifications_id_seq OWNED BY user__notifications.id;


--
-- Name: user__references; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__references (
    id integer NOT NULL,
    ref_user_id integer NOT NULL,
    new_user_id integer,
    new_user_fb_id bigint,
    new_user_email character varying(255) DEFAULT NULL::character varying,
    active boolean NOT NULL,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    activated_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    joined_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.user__references OWNER TO postgres;

--
-- Name: user__references_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__references_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__references_id_seq OWNER TO postgres;

--
-- Name: user__regular; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__regular (
    id integer NOT NULL,
    user_id integer,
    firstname character varying(255) NOT NULL,
    lastname character varying(255) DEFAULT NULL::character varying,
    gender integer,
    birthday date,
    about_me text,
    link character varying(100) DEFAULT NULL::character varying,
    facebook_publish boolean,
    pref_locale character varying(4) DEFAULT NULL::character varying,
    languages text,
    mylike text,
    hotplaces text,
    promotedinitiatives text,
    notshowninfo text,
    postcode character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.user__regular OWNER TO postgres;

--
-- Name: user__regular_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__regular_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__regular_id_seq OWNER TO postgres;

--
-- Name: user__regular_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE user__regular_id_seq OWNED BY user__regular.id;


--
-- Name: user__unsubscribers; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__unsubscribers (
    id integer NOT NULL,
    hashed_email character varying(40) NOT NULL
);


ALTER TABLE public.user__unsubscribers OWNER TO postgres;

--
-- Name: user__unsubscribers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__unsubscribers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__unsubscribers_id_seq OWNER TO postgres;

--
-- Name: user__users; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__users (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    username_canonical character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_canonical character varying(255) NOT NULL,
    enabled boolean NOT NULL,
    salt character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    last_login timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    locked boolean NOT NULL,
    expired boolean NOT NULL,
    expires_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    confirmation_token character varying(255) DEFAULT NULL::character varying,
    password_requested_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    roles text NOT NULL,
    credentials_expired boolean NOT NULL,
    credentials_expire_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    facebookid character varying(255) DEFAULT NULL::character varying,
    twitterid character varying(255) DEFAULT NULL::character varying,
    twitter_username character varying(255) DEFAULT NULL::character varying,
    activity integer,
    ask_again boolean,
    registeredwith character varying(8) NOT NULL,
    registeredwithid character varying(100) NOT NULL,
    is_business boolean NOT NULL,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    managertype boolean,
    typesid character varying(255) DEFAULT NULL::character varying,
    member boolean
);


ALTER TABLE public.user__users OWNER TO postgres;

--
-- Name: COLUMN user__users.roles; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN user__users.roles IS '(DC2Type:array)';


--
-- Name: user__users__notifications; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE user__users__notifications (
    user_id integer NOT NULL,
    notification_id integer NOT NULL
);


ALTER TABLE public.user__users__notifications OWNER TO postgres;

--
-- Name: user__users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user__users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user__users_id_seq OWNER TO postgres;

--
-- Name: user__users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE user__users_id_seq OWNED BY user__users.id;


--
-- Name: usergroup__members; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE usergroup__members (
    id integer NOT NULL,
    usergroup_id integer NOT NULL,
    member_id integer,
    admin integer,
    created_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.usergroup__members OWNER TO postgres;

--
-- Name: usergroup__members_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE usergroup__members_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.usergroup__members_id_seq OWNER TO postgres;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY galleries ALTER COLUMN id SET DEFAULT nextval('galleries_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY identity__verification ALTER COLUMN id SET DEFAULT nextval('identity__verification_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY images ALTER COLUMN id SET DEFAULT nextval('images_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY location ALTER COLUMN id SET DEFAULT nextval('location_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY location__verification ALTER COLUMN id SET DEFAULT nextval('location__verification_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__comments ALTER COLUMN id SET DEFAULT nextval('notice__comments_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__dictionary ALTER COLUMN id SET DEFAULT nextval('notice__dictionary_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__notices ALTER COLUMN id SET DEFAULT nextval('notice__notices_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__property_types ALTER COLUMN id SET DEFAULT nextval('notice__property_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__requests ALTER COLUMN id SET DEFAULT nextval('notice__requests_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__reviews ALTER COLUMN id SET DEFAULT nextval('notice__reviews_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__types ALTER COLUMN id SET DEFAULT nextval('notice__types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__values ALTER COLUMN id SET DEFAULT nextval('notice__values_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY payment__setting ALTER COLUMN id SET DEFAULT nextval('payment__setting_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY share__holder ALTER COLUMN id SET DEFAULT nextval('share__holder_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stickers ALTER COLUMN id SET DEFAULT nextval('stickers_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__notification_group_intervals ALTER COLUMN id SET DEFAULT nextval('user__notification_group_intervals_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__notification_groups ALTER COLUMN id SET DEFAULT nextval('user__notification_groups_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__notifications ALTER COLUMN id SET DEFAULT nextval('user__notifications_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__regular ALTER COLUMN id SET DEFAULT nextval('user__regular_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__users ALTER COLUMN id SET DEFAULT nextval('user__users_id_seq'::regclass);


--
-- Data for Name: document; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY document (id, user_id, path, path2) FROM stdin;
2	1	1381921350_waterfall.jpg	1381920035_rainbow-autumn-colors.jpg
1	2	1383214907_startrail-panorama-Vincent-Brady.jpg	1380279090_norfolk_broads.png
4	10	1384947431_IMG_6467.JPG	1384947394_doodle-4-doodle-2012-childrens-day-141112.jpg
\.


--
-- Name: document_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('document_id_seq', 4, true);


--
-- Data for Name: ext_log_entries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY ext_log_entries (id, action, logged_at, object_id, object_class, version, data, username) FROM stdin;
\.


--
-- Name: ext_log_entries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ext_log_entries_id_seq', 1, false);


--
-- Data for Name: ext_translations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY ext_translations (id, locale, object_class, field, foreign_key, content) FROM stdin;
\.


--
-- Name: ext_translations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('ext_translations_id_seq', 1, false);


--
-- Data for Name: galleries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY galleries (id, regular_id, user_id, notice_id, original_id) FROM stdin;
1	1	\N	\N	\N
2	2	\N	\N	\N
3	3	\N	\N	\N
4	4	\N	\N	\N
5	\N	\N	1	\N
6	\N	\N	2	\N
7	\N	\N	3	\N
8	\N	\N	4	\N
9	5	\N	\N	\N
10	\N	\N	\N	1
49	\N	\N	\N	8
50	\N	\N	\N	7
51	\N	\N	\N	9
52	\N	\N	\N	6
53	\N	\N	\N	5
57	6	\N	\N	\N
58	\N	\N	\N	57
2285	\N	\N	1247	\N
2286	\N	\N	\N	2285
924	\N	\N	544	\N
925	\N	\N	\N	924
2561	\N	\N	1386	\N
2562	\N	\N	\N	2561
3381	\N	\N	1792	\N
3382	\N	\N	\N	3381
3383	\N	\N	1793	\N
3384	\N	\N	\N	3383
2569	\N	\N	1390	\N
2570	\N	\N	\N	2569
3531	\N	\N	1867	\N
3532	\N	\N	\N	3531
1014	\N	\N	589	\N
1015	\N	\N	\N	1014
2787	\N	\N	1496	\N
2788	\N	\N	\N	2787
1312	\N	\N	752	\N
1313	\N	\N	\N	1312
266	\N	\N	\N	3
3267	\N	\N	1735	\N
3268	\N	\N	\N	3267
1688	\N	\N	940	\N
1689	\N	\N	\N	1688
3201	16	\N	\N	\N
2657	\N	\N	1434	\N
2658	\N	\N	\N	2657
2455	\N	\N	1333	\N
2456	\N	\N	\N	2455
2457	\N	\N	1334	\N
2458	\N	\N	\N	2457
3543	\N	\N	1873	\N
3544	\N	\N	\N	3543
2662	8	\N	\N	\N
2663	9	\N	\N	\N
2664	10	\N	\N	\N
1454	\N	\N	823	\N
1455	\N	\N	\N	1454
1456	\N	\N	824	\N
1457	\N	\N	\N	1456
2463	\N	\N	1337	\N
2464	\N	\N	\N	2463
3206	\N	\N	\N	2671
2667	11	\N	\N	\N
2668	12	\N	\N	\N
2669	13	\N	\N	\N
2670	14	\N	\N	\N
2671	15	\N	\N	\N
916	\N	\N	540	\N
917	\N	\N	\N	916
918	\N	\N	541	\N
919	\N	\N	\N	918
2471	\N	\N	1341	\N
2472	\N	\N	\N	2471
2409	\N	\N	1310	\N
2410	\N	\N	\N	2409
2473	\N	\N	1342	\N
2474	\N	\N	\N	2473
2415	\N	\N	1313	\N
2416	\N	\N	\N	2415
3565	\N	\N	1884	\N
3566	\N	\N	\N	3565
3363	\N	\N	1783	\N
3364	\N	\N	\N	3363
3569	\N	\N	1886	\N
3570	\N	\N	\N	3569
\.


--
-- Name: galleries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('galleries_id_seq', 3570, true);


--
-- Data for Name: identity__verification; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY identity__verification (id, user_id, username, status, created_at, activitypoint) FROM stdin;
3	1	jignesh	Requested	2013-10-22 10:20:07	\N
5	3	sakstinat	Requested	2013-10-22 12:50:50	\N
6	2	sakstinat	Verified	2013-11-19 07:21:04	100
\.


--
-- Name: identity__verification_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('identity__verification_id_seq', 6, true);


--
-- Data for Name: images; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY images (id, gallery_id, name, sequence, updated_at, crop) FROM stdin;
93	6	5214bfff8fbc70.png	0	\N	\N
136	8	521484c579d7a0.png	0	\N	\N
811	2463	5253d5844abee0.jpeg	0	\N	\N
509	57	52249d211d9930.png	0	\N	\N
576	51	52242f813eb170.png	0	\N	\N
28	7	52148c4c6a1c60.png	0	\N	\N
29	50	52148c4c6a1c60.png	0	\N	\N
577	51	5226fc6e85e210.png	1	\N	\N
578	51	5226fc7575d1d0.png	2	\N	\N
524	58	52249d211d9930.png	0	\N	\N
41	5	52148f7ba8fb50.png	0	\N	\N
133	52	5214bfff8fbc70.png	0	\N	\N
536	9	52242f813eb170.png	0	\N	\N
538	9	5226fc6e85e210.png	1	\N	\N
539	9	5226fc7575d1d0.png	2	\N	\N
932	1688	524581778a34a0.jpeg	0	\N	\N
814	2471	5253d992f115d0.jpeg	0	\N	\N
815	2471	5253d99cec7230.jpeg	1	\N	\N
816	2471	5253d9b227e800.jpeg	2	\N	\N
935	3267	528f1ace88b480.jpeg	0	\N	\N
936	3268	528f1ace88b480.jpeg	0	\N	\N
897	1	522ad2c9476890.jpeg	0	\N	\N
898	1	525e67fc8e7f30	1	\N	\N
899	1	525e68017ff7e0	2	\N	\N
843	2472	5253d992f115d0.jpeg	0	\N	\N
844	2472	5253d99cec7230.jpeg	1	\N	\N
845	2472	5253d9b227e800.jpeg	2	\N	\N
956	266	5295f2a8c53400.jpeg	0	\N	\N
957	3363	529c5b7c4c89a0.jpeg	0	\N	\N
910	2787	527b230b3f6e00.jpeg	0	\N	\N
958	3381	529c62a4936690.jpeg	0	\N	\N
855	2456	5253cd247c9640.jpeg	0	\N	\N
959	3383	529c668b8f20d0.png	0	\N	\N
858	2473	5253fa50e53740.jpeg	0	\N	\N
960	3383	529c67461e21a0.png	1	\N	\N
860	2474	5253fa50e53740.jpeg	0	\N	\N
961	3531	529f1eddeac270.png	0	\N	\N
863	49	521484c579d7a0.png	0	\N	\N
864	53	52148f7ba8fb50.png	0	\N	\N
962	916	52a05513e4ba10.jpeg	0	\N	\N
810	2455	5253cd247c9640.jpeg	0	\N	\N
\.


--
-- Name: images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('images_id_seq', 962, true);


--
-- Data for Name: location; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY location (id, user_id, notice_id, pgisgeog, pgisgeom, location, printable, usergroup_id) FROM stdin;
1	\N	1	0101000020E610000000638E2FEBCF2A400C04013274424A40	010100000000638E2FEBCF2A400C04013274424A40	Berlin, Germany	\N	\N
29	6	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	Ahmedabad, Gujarat, India	Rajasthan	\N
23	5	\N	0101000020E6100000F098373D5E295240F37FFDC68D393740	0101000000F098373D5E295240F37FFDC68D393740	Gandhinagar, Gujarat, India	\N	\N
1314	\N	1310	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
1317	\N	1313	0101000020E6100000703EAE0D152452402D7C7DAD4B113740	0101000000703EAE0D152452402D7C7DAD4B113740	132 Feet Ring Road, Nava Vadaj, Ahmedabad, GJ, India	\N	\N
1799	\N	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	\N	1
756	\N	752	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
1800	\N	1783	0101000020E61000004E5E64027E2652401E6FF25B74063740	01010000004E5E64027E2652401E6FF25B74063740	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	\N	\N
1378	4	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
1379	3	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
593	\N	589	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
544	\N	540	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
545	\N	541	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
1440	\N	1434	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
548	\N	544	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
1511	\N	1496	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	New Wadaj Ahmedabad	\N	\N
1751	\N	1735	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	Ghatlodiya, Ahmedabad, Gujarat, India	\N	\N
1444	9	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
1809	\N	1792	0101000020E61000004E5E64027E2652401E6FF25B74063740	01010000004E5E64027E2652401E6FF25B74063740	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	\N	\N
1810	\N	1793	0101000020E61000004E5E64027E2652401E6FF25B74063740	01010000004E5E64027E2652401E6FF25B74063740	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	\N	\N
827	\N	823	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
828	\N	824	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
1392	\N	1386	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
1449	13	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
1396	\N	1390	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
1337	\N	1333	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
944	\N	940	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
1338	\N	1334	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
1450	12	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
1451	11	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
1341	\N	1337	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
1345	\N	1341	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
3	\N	3	0101000020E610000000638E2FEBCF2A400C04013274424A40	010100000000638E2FEBCF2A400C04013274424A40	Berlin, Germany	\N	\N
1346	\N	1342	0101000020E6100000E338F06A392452405A7AD91B210A3740	0101000000E338F06A392452405A7AD91B210A3740	Ahmedabad, Gujarat, India	\N	\N
1448	14	\N	0101000020E6100000F098373D5E295240F37FFDC68D393740	0101000000F098373D5E295240F37FFDC68D393740	\N	\N	\N
30	2	\N	0101000020E6100000D095E6FB4E2252405CE84A04AA133740	0101000000D095E6FB4E2252405CE84A04AA133740	Ghatlodiya, Ahmedabad, Gujarat, India	\N	\N
4	\N	4	0101000020E610000000638E2FEBCF2A400C04013274424A40	010100000000638E2FEBCF2A400C04013274424A40	Berlin, Germany	\N	\N
1884	\N	1867	0101000020E6100000CC95E6FB4E2252405CE84A04AA133740	0101000000CC95E6FB4E2252405CE84A04AA133740	Ghatlodiya, Ahmedabad, Gujarat, India	\N	\N
1443	10	\N	0101000020E6100000900A2AAA7E1E52409B6E7E688B083740	0101000000900A2AAA7E1E52409B6E7E688B083740	Paramdham, Bopal, Ahmedabad, Gujarat 380058, India	\N	\N
1447	15	\N	0101000020E6100000505E64027E2652401E6FF25B74063740	0101000000505E64027E2652401E6FF25B74063740	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	\N	\N
1251	\N	1247	0101000020E6100000CF6740BD1928524015ADDC0BCC123740	0101000000CF6740BD1928524015ADDC0BCC123740	Airport Road, Sardar Nagar, Hansol, Ahmedabad, Gujarat, 380003, India	\N	\N
2	\N	2	0101000020E610000000638E2FEBCF2A400C04013274424A40	010100000000638E2FEBCF2A400C04013274424A40	Berlin, Germany	\N	\N
1886	\N	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	Ghatlodiya, Ahmedabad, Gujarat, India	\N	2
1891	\N	1873	0101000020E6100000F498373D5E295240F37FFDC68D393740	0101000000F498373D5E295240F37FFDC68D393740	Gandhinagar, Gujarat, India	\N	\N
1718	16	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	\N	\N	\N
1442	8	\N	0101000020E6100000200DB8F8A520524086200725CC023740	0101000000200DB8F8A520524086200725CC023740	Prahalad nagare ahmedabad gujarat	\N	\N
5	1	\N	0101000020E610000000000000000000000000000000000000	010100000000000000000000000000000000000000	Sector 11, Gandhinagar, Gujarat, India	\N	\N
1902	\N	1884	0101000020E6100000CC95E6FB4E2252405CE84A04AA133740	0101000000CC95E6FB4E2252405CE84A04AA133740	Ghatlodiya, Ahmedabad, Gujarat, India	\N	\N
1904	\N	1886	0101000020E61000004E5E64027E2652401E6FF25B74063740	01010000004E5E64027E2652401E6FF25B74063740	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	\N	\N
\.


--
-- Data for Name: location__verification; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY location__verification (id, user_id, username, password, status, created_at, activitypoint, lastname, pincode, address, additionaladdress) FROM stdin;
2	1	meisele	kOPeNblw	Requested	2013-10-16 10:24:13	\N	\N	\N	\N	\N
1	10	Bhumi	TxcaB8WE	Requested	2013-09-18 12:19:34	\N			Ghatlodiya, Ahmedabad, Gujarat, India	
4	2	Bhumi	weFNOpoG	Requested	2013-11-21 12:26:42	\N	Gothi	2222222	Ghatlodiya, Ahmedabad, Gujarat, India	Shantaram Akhabar nagar
5	15	test	WoFIODgA	Requested	2013-11-21 12:58:20	\N	aaa	2122	Laxmi Bazar, Saraspur, Ahmedabad, GJ, India	test
3	8	Bhumi	SKzO38eH	Requested	2013-10-30 10:52:29	\N			Prahalad nagare ahmedabad gujarat	
\.


--
-- Name: location__verification_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('location__verification_id_seq', 5, true);


--
-- Name: location_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('location_id_seq', 1904, true);


--
-- Data for Name: message__about_notice; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY message__about_notice (message_id, about_notice_id) FROM stdin;
\.


--
-- Data for Name: message__about_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY message__about_user (message_id, about_user_id) FROM stdin;
\.


--
-- Data for Name: message__messages; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY message__messages (id, sender_user_id, receiver_user_id, first_message_id, prev_message_id, next_message_id, title, content, read, created_at, sender_deleted_at, receiver_deleted_at, system, usergroup_id, fromgroup) FROM stdin;
61	2	5	9	9	62	aaaaaaaaaaaa	Hi,,,,,,,	f	2013-11-27 13:37:26	\N	\N	f	\N	\N
2	1	2	1	1	\N	Hi	Ya sure you can come at 8:00 pm at our society.	f	2013-10-08 13:28:42	\N	\N	f	\N	\N
1	2	1	1	\N	2	Hi	I want to participate in navaratri.	t	2013-10-08 13:22:38	\N	\N	f	\N	\N
3	1	2	3	\N	\N	Hi	Hi	f	2013-10-11 10:03:09	\N	\N	f	\N	\N
11	2	5	11	\N	59	Hi	I got your message....	f	2013-10-14 05:38:41	\N	\N	f	\N	\N
4	1	2	4	\N	\N	Hello	I want to join.............	f	2013-10-11 13:50:15	\N	\N	f	\N	\N
59	2	5	11	11	60	Hi	Hi, I will give response as soon as possible.	f	2013-11-27 13:23:43	\N	\N	f	\N	\N
5	1	2	5	\N	\N	Hello	I want to join.............	f	2013-10-11 13:51:08	\N	\N	f	\N	\N
6	1	2	6	\N	\N	Hello	I want to join.............	f	2013-10-11 13:55:02	\N	\N	f	\N	\N
9	5	2	9	\N	61	aaaaaaaaaaaa	aaaaaaaaaaaaaaaaaaaa	t	2013-10-11 14:06:46	\N	\N	f	\N	\N
7	1	2	7	\N	\N	Hello	Hello.....	f	2013-10-11 13:55:37	\N	\N	f	\N	\N
8	1	2	8	\N	\N	dddddd	dddddddd	f	2013-10-11 14:04:51	\N	\N	f	\N	\N
63	2	1	63	\N	64	hello	Hello this is test message	f	2013-11-28 10:55:53	\N	\N	f	\N	\N
10	1	3	10	\N	\N	dddddddddd	dsssssssss	f	2013-10-11 14:10:09	\N	\N	f	\N	\N
65	1	2	65	\N	\N	test	Test message for notification	f	2013-11-28 11:35:38	\N	\N	f	\N	\N
12	2	1	12	\N	\N	Hello	you can.	t	2013-10-21 05:36:28	\N	\N	f	\N	\N
13	2	1	13	\N	\N	Hi	When you will organize this again....?	t	2013-10-21 07:33:47	\N	\N	f	\N	\N
67	1	2	67	\N	\N	Wow done	Thanks.	f	2013-11-28 11:39:05	\N	\N	f	\N	\N
14	2	1	14	\N	\N	Hello	Please keep contact with me.	t	2013-10-21 09:34:28	\N	\N	f	\N	\N
15	2	1	15	\N	\N	Hi	I Will come every Sunday.	f	2013-10-24 12:40:06	\N	\N	f	\N	\N
69	1	2	69	\N	\N	Hi,,	Hi,,,,,,,,	f	2013-11-28 11:57:33	\N	\N	f	\N	\N
16	2	1	16	\N	\N	Hi	How we will arrange this...!	f	2013-10-24 12:48:20	\N	\N	f	\N	\N
17	2	1	17	\N	\N	What is about event	When next time we will arrange this	f	2013-10-24 12:51:20	\N	\N	f	\N	\N
18	2	1	18	\N	\N	Hello	Please keep in contact	f	2013-10-24 12:54:29	\N	\N	f	\N	\N
21	2	1	21	\N	\N	Hello	Hello	f	2013-10-29 14:30:37	\N	\N	f	\N	\N
22	2	5	22	\N	\N	New Created Group Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-01 05:21:15	\N	\N	f	\N	\N
23	2	5	23	\N	\N	New Created Group Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-01 05:23:05	\N	\N	f	\N	\N
24	2	5	24	\N	\N	New Created Group Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-01 05:25:38	\N	\N	f	\N	\N
25	2	5	25	\N	\N	New Created Group Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-01 05:31:20	\N	\N	f	\N	\N
26	2	5	26	\N	\N	New Created Group Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-01 05:33:37	\N	\N	f	\N	\N
27	2	5	27	\N	\N	New Created Group Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-01 05:50:56	\N	\N	f	\N	\N
28	1	2	28	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:41:18	\N	\N	f	\N	\N
29	1	2	29	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:42:31	\N	\N	f	\N	\N
30	1	2	30	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:43:19	\N	\N	f	\N	\N
31	1	2	31	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:44:00	\N	\N	f	\N	\N
32	1	2	32	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:45:58	\N	\N	f	\N	\N
33	1	2	33	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:46:58	\N	\N	f	\N	\N
34	1	2	34	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:47:36	\N	\N	f	\N	\N
35	1	2	35	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:48:11	\N	\N	f	\N	\N
36	1	2	36	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:48:16	\N	\N	f	\N	\N
38	1	2	38	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:50:52	\N	\N	f	\N	\N
49	1	2	49	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-06 09:04:59	\N	\N	f	\N	\N
48	1	2	48	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-06 09:03:03	\N	\N	f	\N	\N
40	1	2	40	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:53:34	\N	\N	f	\N	\N
41	1	2	41	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:53:56	\N	\N	f	\N	\N
39	1	2	39	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-06 08:51:40	\N	\N	f	\N	\N
60	2	5	11	59	\N	Hi	Sorry, you have to wait for my reply.	f	2013-11-27 13:34:51	\N	\N	f	\N	\N
43	1	2	43	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:55:36	\N	\N	f	\N	\N
62	2	5	9	61	\N	aaaaaaaaaaaa	Test Test Test	f	2013-11-27 13:43:03	\N	\N	f	\N	\N
44	1	2	44	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:56:56	\N	\N	f	\N	\N
37	1	2	37	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-06 08:49:06	\N	\N	f	\N	\N
45	1	2	45	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:57:40	\N	\N	f	\N	\N
42	1	2	42	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-06 08:54:42	\N	\N	f	\N	\N
46	1	2	46	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 08:59:54	\N	\N	f	\N	\N
64	2	1	63	63	\N	hello	Test Mail	f	2013-11-28 10:57:30	\N	\N	f	\N	\N
47	1	2	47	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 09:02:27	\N	\N	f	\N	\N
66	1	2	66	\N	\N	Wow done	Thanks.	f	2013-11-28 11:38:58	\N	\N	f	\N	\N
68	1	2	68	\N	\N	bhumi	Bhumi	f	2013-11-28 11:42:01	\N	\N	f	\N	\N
51	1	10	51	\N	\N	New Created Listing Invitation	Hello, Hardik                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 09:08:02	\N	\N	f	\N	\N
70	1	2	70	\N	\N	Hi,,	Hi,,,,,,,,	f	2013-11-28 11:57:40	\N	\N	f	\N	\N
71	1	2	71	\N	\N	Finally solved	great Task.....	f	2013-11-28 12:18:17	\N	\N	f	\N	\N
53	1	5	53	\N	\N	New Created Listing Invitation	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 09:08:02	\N	\N	f	\N	\N
50	1	2	50	\N	\N	regularuser.message.subject_Notice	Hello, sakstinat                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-06 09:07:56	\N	\N	f	\N	\N
72	1	2	72	\N	\N	qqqqqq	qqqqqqq	f	2013-11-28 12:22:52	\N	\N	f	\N	\N
55	2	10	55	\N	\N	New Created Listing Invitation	Hello, Hardik                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-07 05:10:50	\N	\N	f	\N	\N
57	2	5	57	\N	\N	regularuser.message.subject_Notice	Hello, Betty                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-07 05:28:57	\N	\N	f	\N	\N
54	2	1	54	\N	\N	regularuser.message.subject_Notice	Hello, Jignesh                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-07 05:10:49	\N	\N	f	\N	\N
58	2	1	58	\N	\N	regularuser.message.subject_Notice	Hello, Jignesh                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	t	2013-11-07 05:29:01	\N	\N	f	\N	\N
52	1	\N	52	\N	\N	New Created Listing Invitation	Hello, Bhumi                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-06 09:08:02	\N	\N	f	\N	\N
56	2	\N	56	\N	\N	New Created Listing Invitation	Hello, Bhumi                                                                    This is Group Informatiopn which is Created latest                                                                    From Polly & Bob Team	f	2013-11-07 05:10:50	\N	\N	f	\N	\N
73	15	10	73	\N	\N	New created Listing invitation	 Hello, Hardik                                                                    Hardik invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:10:46	\N	\N	f	\N	\N
74	15	8	74	\N	\N	New created Listing invitation	 Hello, Bhumi                                                                    Bhumi invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:10:47	\N	\N	f	\N	\N
75	15	5	75	\N	\N	New created Listing invitation	 Hello, Betty                                                                    Betty invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:10:47	\N	\N	f	\N	\N
76	15	10	76	\N	\N	New created Listing invitation	 Hello, Hardik                                                                    Hardik invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:38:06	\N	\N	f	\N	\N
77	15	8	77	\N	\N	New created Listing invitation	 Hello, Bhumi                                                                    Bhumi invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:38:06	\N	\N	f	\N	\N
78	15	5	78	\N	\N	New created Listing invitation	 Hello, Betty                                                                    Betty invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:38:06	\N	\N	f	\N	\N
79	15	10	79	\N	\N	New created Listing invitation	 Hello, Hardik                                                                    Hardik invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:57:12	\N	\N	f	\N	\N
80	15	8	80	\N	\N	New created Listing invitation	 Hello, Bhumi                                                                    Bhumi invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:57:12	\N	\N	f	\N	\N
81	15	5	81	\N	\N	New created Listing invitation	 Hello, Betty                                                                    Betty invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-02 10:57:12	\N	\N	f	\N	\N
84	2	1	84	\N	\N	Hi	Please add me in your neighbour list	f	2013-12-03 13:16:59	\N	\N	f	\N	\N
85	2	1	85	\N	\N	Hello	Please give me reply	f	2013-12-03 13:19:42	\N	\N	f	\N	\N
82	2	5	82	\N	\N	Hello	I will accept your request later	t	2013-12-03 08:53:01	\N	\N	f	\N	\N
83	2	5	83	\N	\N	Hi	please accept my request.	t	2013-12-03 13:14:16	\N	\N	f	\N	\N
86	2	5	86	\N	\N	New created Listing invitation	Hello, Betty                                                                    Betty invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-04 10:59:02	\N	\N	f	\N	\N
87	2	5	87	\N	\N	New created Listing invitation	Hello, Betty                                                                    Betty invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-04 11:02:27	\N	\N	f	\N	\N
88	2	5	88	\N	\N	New created Listing invitation	Hello, Betty                                                                    Betty %user% invited you to this Listing on Polly & Bob.                                                                    From Polly & Bob	f	2013-12-04 12:26:10	\N	\N	f	\N	\N
89	2	5	89	\N	\N	New created Group invitation	Hello, Betty                                                                    The Group %groupname% on Polly & Bob was created lately by %administrator%. Have a look at this link:The Group Multicore employess on Polly & Bob was created lately by %administrator%. Have a look at this link:                                                                    From Polly & Bob	t	2013-12-04 12:39:35	\N	\N	f	\N	\N
90	5	2	90	\N	\N	New created Listing invitation	Hello, Bhumi                                                                    Bhumi %user% invited you to this Listing on Polly & Bob./app_dev.php/i-need-tables-and-chairs/2013/12/02                                                                    From Polly & Bob	t	2013-12-04 12:56:14	\N	\N	f	\N	\N
91	5	2	91	\N	\N	New created Listing invitation	Hello, Bhumi                                                                    Bhumi Betty invited you to this Listing on Polly & Bob.<a href=http://pollyandbob.sf/app_dev.php/i-need-tables-and-chairs/2013/12/02>Click  Here</a>                                                                    From Polly & Bob	f	2013-12-05 05:22:10	\N	\N	f	\N	\N
92	5	2	92	\N	\N	New created Listing invitation	text/html	f	2013-12-05 05:52:31	\N	\N	f	\N	\N
93	5	2	93	\N	\N	New created Listing invitation	Hello, Bhumi                                                                    Bhumi Betty invited you to this Listing on Polly & Bob.<a href=http://pollyandbob.sf/app_dev.php/i-need-tables-and-chairs/2013/12/02>Click  Here</a>                                                                    From Polly & Bob	f	2013-12-05 05:54:34	\N	\N	f	\N	\N
94	5	2	94	\N	\N	New created Listing invitation	<html>\n    <body style="font-family: MYRIADPRO-REGULAR, sans-serif;">\n        <table style="border: 1px #000 solid; width: 100%; border-collapse: collapse;">\n            <tr style="\n                height: 61px;\n                background-color: #000000;\n                border: 3px #000000 solid;\n                background-image: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(1,1,1,0.1) 100%); /* FF3.6+ */\n                background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0)), color-stop(100%,rgba(1,1,1,0.1))); /* Chrome,Safari4+ */\n                background-image: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(1,1,1,0.1) 100%); /* Chrome10+,Safari5.1+ */\n                background-image: -o-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(1,1,1,0.1) 100%); /* Opera 11.10+ */\n                background-image: -ms-linear-gradient(top, rgba(255,255,255,0) 0%,rgba(1,1,1,0.1) 100%); /* IE10+ */\n                background-image: linear-gradient(to top, rgba(1,1,1,0.1) 0%, rgba(255,255,255,0) 100%); /* W3C */    \n                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1affffff', endColorstr='#1a010101',GradientType=0 );\n                ">\n\t\t\t\t<td style="padding: 12px 0 5px 20px">\n                <img src="http://pollyandbob.sf/images/logo.png" height="94px" alt="Polly&Bob" />\n\t\t\t\t</td>\n            </tr>\n            <tr style="\n                border: 3px #000 solid;\n                border-top: none;\n                ">\n\t\t\t\t<td style="padding: 20px;">\n\t\t\t\t\t\t\t\t\t<p style="margin: 30px 0">\n                                        Dear User,\n                                    </p>\n                                    <p style="margin: 30px 0">\n                                       Betty invited you to this Listing on Polly &amp; Bob.\n                                    </p>\n                                    <p>\n                                    \t<a href="http://pollyandbob.sf/app_dev.php/i-need-tables-and-chairs/2013/12/02">Click Here</a>\t\n                                    </p>\n                                    <p style="margin: 40px 0">\n                                        From Polly &amp; Bob\n                                    </p>\n                                    \n                                    \n\t\t\t\t</td>\n            </tr>\n        </table>\n    </body>\n</html>	f	2013-12-05 06:07:37	\N	\N	f	\N	\N
96	2	\N	96	\N	\N	Review writen notification	review	f	2013-12-09 07:44:31	\N	\N	f	\N	\N
97	2	\N	97	\N	\N	Review writen notification	review	f	2013-12-09 07:47:29	\N	\N	f	\N	\N
98	2	\N	98	\N	\N	Review writen notification	review	f	2013-12-09 08:34:31	\N	\N	f	\N	\N
99	2	1	99	\N	\N	Review writen notification	review	f	2013-12-09 08:38:35	\N	\N	f	\N	\N
102	2	1	102	\N	\N	Review writen notification	review	f	2013-12-09 09:31:29	\N	\N	f	\N	\N
103	2	1	103	\N	\N	Review writen notification	review	f	2013-12-09 09:56:57	\N	\N	f	\N	\N
105	1	2	105	\N	\N	Jigneshhas send request	I want 1 piece	t	2013-12-09 10:18:51	\N	\N	f	\N	\N
104	1	2	104	\N	\N	Review writen notification	review	t	2013-12-09 10:11:41	\N	\N	f	\N	\N
106	1	2	106	\N	\N	Hello, Bhumi	subjec	t	2013-12-09 10:46:17	\N	\N	f	\N	\N
\.


--
-- Name: message__messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('message__messages_id_seq', 106, true);


--
-- Data for Name: notice__comments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__comments (id, author_id, notice_id, user_id, title, text, type, created_at, is_read, usergroup_id) FROM stdin;
2	1	940	2	Car	For Income it is good idea	1	2013-10-02 10:11:12	t	\N
1	2	1	1	Bike	Is this in working position?	1	2013-10-01 09:42:34	t	\N
3	1	940	2	Car to use	Any one used your car.	1	2013-10-08 06:16:49	f	\N
4	1	824	2	car	Hi, Nice service...	1	2013-10-08 06:22:57	f	\N
5	1	824	2	car	But, you are doing this for little income...?	1	2013-10-08 06:25:36	f	\N
6	1	824	2	car	Hay you not replied.....	1	2013-10-08 06:27:58	f	\N
7	1	824	2	car	reply please	1	2013-10-08 06:30:02	f	\N
8	1	824	2	car	I want your car for five days...	1	2013-10-08 06:36:06	f	\N
10	2	4	1	Book Swap Tea Party	Great	1	2013-10-09 05:36:40	f	\N
11	2	4	1	Book Swap Tea Party	I will come.	1	2013-10-09 05:46:01	f	\N
18	2	940	2	Car to use	yes you want	1	2013-10-22 14:20:31	f	\N
19	2	940	2	Car to use	yes you want?	1	2013-10-22 14:21:49	f	\N
20	2	940	2	Car to use	Do you want?	1	2013-10-22 14:22:53	f	\N
21	2	1247	2	find neighbour	No one want to come?	1	2013-10-25 06:56:05	f	\N
22	2	1386	1	Baby sitter	Polly And Bob will help	1	2013-11-15 09:32:41	f	\N
17	3	940	2	Car to use	yes	1	2013-10-22 09:50:51	f	\N
23	2	940	2	Car to use	test	1	2013-11-28 10:51:06	f	\N
24	2	1496	2	Rainy season Help to you	test	1	2013-11-29 08:54:31	f	\N
25	2	1496	2	Rainy season Help to you	this is test comment	1	2013-11-29 08:56:23	f	\N
26	2	1496	2	Rainy season Help to you	this is Last try comment	1	2013-11-29 08:59:49	f	\N
27	2	1496	2	Rainy season Help to you	this is Last to last  try comment	1	2013-11-29 09:02:09	f	\N
28	2	1496	2	Rainy season Help to you	this is Last to last to last  try comment	1	2013-11-29 09:13:36	f	\N
29	2	1496	2	Rainy season Help to you	aaaaaa	1	2013-11-29 09:23:05	f	\N
30	2	1496	2	Rainy season Help to you	hi	1	2013-11-29 09:23:53	f	\N
\.


--
-- Name: notice__comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__comments_id_seq', 30, true);


--
-- Data for Name: notice__dictionary; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__dictionary (id, text, tag, quantity_search, quantity_tag, search_activated, tag_activated, disabled) FROM stdin;
1	Trust	f	7	0	t	t	f
6	Google	t	2	0	t	t	f
4	Doodle	f	6	0	f	t	f
3	Art	t	0	4	t	t	f
2	Arts	t	0	53	t	t	f
10	Jjj	t	0	2	t	t	t
9	A	t	0	2	t	t	t
8	Jigs	t	0	1	t	t	t
7	Doodlwe	t	1	0	t	t	t
5	To give	t	0	2	t	t	t
14	Seminar	t	0	2	f	f	f
15	Cat	t	0	2	f	f	f
16	Rain Help	t	0	2	f	f	f
17	Rainy help	t	0	2	f	f	f
11	My car	t	0	6	t	t	t
12	To use	t	0	6	t	t	t
18	Farming	t	0	4	f	f	f
13	Navaratri	t	0	9	t	t	t
19	Car to use	t	0	2	f	f	f
20	Class furniture	t	0	2	f	f	f
21	Computer	t	0	3	f	f	f
\.


--
-- Name: notice__dictionary_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__dictionary_id_seq', 21, true);


--
-- Data for Name: notice__facebook_feed; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__facebook_feed (id, notice_id, fbuserid, photoid, postid) FROM stdin;
\.


--
-- Name: notice__facebook_feed_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__facebook_feed_id_seq', 1, false);


--
-- Data for Name: notice__notices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__notices (id, type_id, user_id, title, content, allowed, draft, link, created_at, start_date, end_date, slug, on_dashboard, tags, date_arrange, start_time_arrange, location_arrange, end_time_arrange, end_time, start_time, pieces, one_piece, currency, price, free, unlimited, end_date_arrange, spot, default_setting, usergroup_id, completed) FROM stdin;
3	2	1	New in town	New in Berlin? Join us to explore the city. Restaurants, clubs, streets, places. Hang out, walk around, have fun.	\N	f	\N	2013-04-29 09:02:00	2013-04-29 09:02:00	2013-04-29 09:02:00	new-in-town	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
824	10	2	car	If any one want a car for tour. I have.	\N	f	\N	2013-09-26 08:47:34	\N	\N	car	f	\N	t	f	f	f	11:00:00	05:00:00	\N	t	rs	200.00	f	f	\N	\N	\N	\N	\N
1333	12	1	Navaratri festival 2013	Navaratri festival 2013	\N	f	\N	2013-10-08 09:14:04	2013-10-11 00:00:00	2013-10-22 00:00:00	navaratri-festival-2013	f	Navaratri	f	f	t	f	23:55:00	19:15:00	\N	f	dollar	\N	t	t	f	\N	\N	\N	\N
1337	9	1	She is very helpfully	To Find theft she is helpful.	\N	f	\N	2013-10-08 09:50:43	\N	\N	she-is-very-helpfully	f	Cat	t	f	t	f	18:00:00	07:00:00	\N	f	dollar	\N	f	f	f	\N	\N	\N	\N
589	3	2	Help	need help	\N	f	\N	2013-09-24 10:35:32	\N	\N	help	f	\N	t	f	t	f	\N	\N	\N	f	rs	\N	f	f	\N	\N	\N	\N	\N
752	4	2	Bike	I need old bike.	\N	f	\N	2013-09-25 11:52:43	\N	\N	bike	f	\N	t	t	f	t	\N	\N	\N	t	rs	\N	f	f	\N	\N	\N	\N	\N
541	4	2	Php-symfony	php-symfony gg	\N	f	\N	2013-09-24 07:42:51	2013-09-30 00:00:00	2013-09-30 00:00:00	php-symfony	f	\N	f	f	f	f	03:00:00	03:00:00	\N	t	rs	\N	f	f	\N	\N	\N	\N	\N
1341	11	1	Rainy season Help	We are giving Rainy season Help	\N	f	\N	2013-10-08 10:08:09	\N	\N	rainy-season-help	f	Rain Help	t	t	f	f	\N	\N	\N	f	dollar	10.00	f	f	f	\N	\N	\N	\N
544	4	2	Laptop	to use it for one day	\N	f	\N	2013-09-24 09:14:13	2013-09-30 00:00:00	2013-09-30 00:00:00	laptop	f	\N	f	f	f	f	22:00:00	09:00:00	\N	t	rs	\N	f	f	\N	\N	\N	\N	\N
1792	10	15	We have car in group.	IF any one interested join with us.	\N	f	\N	2013-12-02 10:36:13	2013-12-04 00:00:00	2013-12-04 00:00:00	we-have-car-in-group	f	car to use	f	t	f	f	\N	\N	\N	t	Euro	\N	t	f	f	\N	f	1	\N
1247	1	2	find neighbour	find neighbour who want to come on tour	\N	f	\N	2013-10-01 11:42:44	\N	\N	find-neighbour	f	\N	f	f	f	f	\N	\N	\N	f	rs	\N	f	f	f	\N	\N	\N	\N
823	10	2	Mobile	I want sell my camera	\N	f	\N	2013-09-26 05:14:55	2013-09-26 00:00:00	\N	mobile	f	\N	f	t	f	t	\N	\N	\N	t	rs	8000.00	f	f	\N	\N	\N	\N	\N
1793	4	15	I need tables and chairs	I need tables and chairs I want to start class	\N	f	\N	2013-12-02 10:52:43	2013-12-03 00:00:00	2013-12-03 00:00:00	i-need-tables-and-chairs	f	class furniture	f	t	f	f	\N	\N	15	f	Euro	\N	f	f	f	\N	f	\N	\N
1310	12	2	Get to Gether	We will meet on Saturday and will do lot's of fun.	\N	f	\N	2013-10-03 11:08:25	\N	\N	get-to-gether	f	Navaratri	t	t	t	f	\N	\N	\N	f	rs	\N	t	t	f	\N	\N	\N	\N
1313	12	2	Navaratri festival	In our society navaratri is organized. enjoy it.	\N	f	\N	2013-10-07 09:10:00	2013-10-07 00:00:00	2013-10-13 00:00:00	navaratri-festival	f	\N	f	f	f	f	23:55:00	20:15:00	\N	f	dollar	\N	t	t	f	\N	\N	\N	\N
2	3	1	Monday Mothers Meetup	We are young mothers from Friedrichshain. We meet every Monday evening. Sometimes we organize babysitting and kids stuff. But generally we just like to hang out with other women from the hood, without men.	\N	f	\N	2013-04-29 09:01:00	2013-04-29 09:01:00	2013-04-29 09:01:00	monday-mothers-meetup	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	currency	\N	\N	\N	\N	\N	\N	\N	\N
1434	5	5	hardik	sas	\N	f	\N	2013-10-29 12:46:37	\N	\N	asa	f	\N	t	t	f	f	\N	\N	\N	f	dollar	\N	f	f	f	\N	f	\N	\N
1867	10	2	Offer comupter to use	This Company Offer computer to use.	\N	f	\N	2013-12-04 12:22:40	\N	\N	offer-comupter-to-use	f	Computer	t	t	f	f	\N	\N	5	f	Euro	\N	t	f	f	\N	f	\N	\N
940	10	2	Car to use	If any one want to go for tour can use my car	\N	f	\N	2013-09-27 13:00:27	2013-12-20 00:00:00	2013-12-29 00:00:00	car-to-use	t	My car,To use	f	f	f	f	19:00:00	09:00:00	\N	t	Euro	100.00	f	f	f	\N	t	\N	\N
1873	13	5	\N	\N	\N	t	\N	2013-12-05 06:06:18	\N	\N	\N	f	\N	f	f	f	f	\N	\N	\N	f	\N	\N	f	f	f	\N	f	\N	\N
1884	12	2	\N	\N	\N	t	\N	2013-12-06 10:32:21	\N	\N	\N	f	\N	f	f	f	f	\N	\N	\N	f	\N	\N	f	f	f	\N	f	\N	f
540	10	2	computer	To use it for one day	\N	f	\N	2013-09-24 07:37:17	2013-09-30 00:00:00	2013-09-30 00:00:00	computer	f	computer	f	f	f	f	20:00:00	17:00:00	12	f	Euro	10.00	f	f	f	0	t	\N	f
1	10	1	Buy my old bike.	Always gave me a good ride. Have it for 200 €!	\N	f	\N	2013-04-29 09:00:00	2013-04-29 09:00:00	2013-04-29 09:00:00	buy-my-old-bike	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	dollar	200.00	f	\N	\N	\N	\N	\N	\N
1496	3	2	Rainy season Help to you	Rainy season Help to you.	\N	f	\N	2013-11-07 05:19:35	2013-12-01 00:00:00	2013-11-30 00:00:00	rainy-season-help-to-you	f	Rainy help	f	f	t	f	21:00:00	01:00:00	\N	f	dollar	\N	f	f	f	\N	f	\N	\N
1390	12	1	\N	\N	\N	t	\N	2013-10-23 09:52:56	\N	\N	\N	f	\N	f	f	f	f	\N	\N	\N	f	\N	\N	f	f	f	\N	f	\N	\N
1886	12	15	\N	\N	\N	t	\N	2013-12-06 12:27:32	\N	\N	\N	f	\N	f	f	f	f	\N	\N	\N	f	\N	\N	f	f	f	\N	f	\N	f
1342	13	1	Navaratri festival 2013	Navaratri festival 2013	\N	f	\N	2013-10-08 11:11:26	2013-10-11 00:00:00	2013-10-22 00:00:00	navaratri-festival-2013-1	f	Navaratri	f	f	t	f	23:55:00	19:15:00	\N	f	dollar	125.00	f	t	f	\N	\N	\N	\N
1386	5	1	Baby sitter	I want Baby Sitter	\N	f	\N	2013-10-23 09:37:43	2013-10-11 00:00:00	2013-10-11 00:00:00	baby-sitter	f	\N	f	f	t	f	\N	\N	\N	f	currency	\N	f	f	f	\N	f	\N	\N
4	12	1	Book Swap Tea Party	We have a tea, everybody bring 2 books. We discuss and exchange, meet nice people, enjoy a laid back Sunday afternoon in changing places serving excellent teas. Go home with a new book.	\N	f	\N	2013-04-30 09:03:00	2013-04-29 09:03:00	2013-04-29 09:03:00	book-swap-tea-party	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	euro	10.00	\N	\N	\N	1	\N	\N	f
1334	6	1	seminar	Arrange seminar on symfony2 new features..	\N	f	\N	2013-10-08 09:22:24	2013-10-12 00:00:00	2013-10-12 00:00:00	seminar	f	seminar	f	f	t	f	15:00:00	10:30:00	\N	f	dollar	\N	f	f	f	\N	\N	\N	\N
1783	12	15	Navaratri festival 2014	We are going to arrange navaratri festival	\N	f	\N	2013-12-02 10:05:33	2013-12-31 00:00:00	2013-12-31 00:00:00	navaratri-festival-2014	f	navaratri	f	f	t	f	22:55:00	20:00:00	\N	f	Euro	\N	t	t	f	\N	f	1	\N
1735	10	2	Goods For farming	Goods For farming	\N	f	\N	2013-11-22 08:48:49	\N	\N	goods-for-farming	f	Farming	t	t	f	f	\N	\N	9	f	Euro	5.00	f	f	f	\N	t	\N	\N
\.


--
-- Data for Name: notice__notices__dictionaries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__notices__dictionaries (dictionary_id, notice_id) FROM stdin;
13	1310
13	1333
14	1334
15	1337
16	1341
13	1342
17	1496
11	940
12	940
18	1735
13	1783
19	1792
20	1793
21	1867
21	540
\.


--
-- Name: notice__notices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__notices_id_seq', 1886, true);


--
-- Data for Name: notice__property_types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__property_types (id, name, element, options, expanded, multiple) FROM stdin;
4	price	1	\N	f	f
2	offergoods	3	a:6:{i:1;s:3:"buy";i:2;s:13:"propose_price";i:3;s:12:"borrow_bring";i:4;s:6:"use_it";i:5;s:7:"swap_it";i:6;s:9:"take_free";}	f	f
5	contact	3	a:1:{i:1;s:10:"contact_me";}	f	f
3	direction	3	a:2:{i:1;s:5:"offer";i:2;s:4:"need";}	f	f
6	what	3	a:5:{i:1;s:6:"sports";i:2;s:5:"party";i:3;s:4:"arts";i:4;s:5:"music";i:5;s:5:"other";}	f	f
7	offerservice	3	a:2:{i:1;s:3:"buy";i:2;s:18:"propose_time_price";}	f	f
8	findservice	3	a:1:{i:1;s:18:"propose_time_price";}	f	f
9	offerevent	3	a:4:{i:1;s:10:"buy_a_spot";i:2;s:13:"propose_price";i:3;s:12:"reserve_spot";i:4;s:9:"take_part";}	f	f
1	findgoods	3	a:5:{i:1;s:7:"to_sell";i:2;s:7:"to_lend";i:3;s:6:"to_use";i:4;s:7:"to_swap";i:5;s:7:"to_give";}	t	t
\.


--
-- Name: notice__property_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__property_types_id_seq', 4, true);


--
-- Data for Name: notice__requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__requests (id, author_id, notice_id, user_id, title, text, status, created_at, is_read, requeststatus, piece_spot, price, free, proposeprice, totalprice, currency, is_read_status) FROM stdin;
33	1	940	2	Car to use	ssss	pending	2013-12-05 13:17:02	t	pending	\N	0.00	f	0.00	0.00	\N	t
31	1	940	2	Car to use	sa	pending	2013-12-05 13:07:07	t	pending	\N	0.00	f	0.00	0.00	\N	t
29	1	940	2	Car to use	Hi	pending	2013-12-05 13:05:26	t	pending	\N	0.00	f	0.00	0.00	\N	t
34	1	540	2	computer	Hi	pending	2013-12-05 13:17:32	t	pending	2	11.00	f	11.00	22.00	EURO	t
32	1	540	2	computer	sss	pending	2013-12-05 13:07:28	t	pending	\N	10.00	f	0.00	0.00	\N	t
30	1	540	2	computer	ddd	pending	2013-12-05 13:05:47	t	pending	\N	10.00	f	0.00	0.00	\N	t
14	2	1	1	Buy my old bike.	Hi	pending	2013-11-21 06:09:07	t	pending	\N	0.00	f	0.00	0.00	\N	t
1	2	2	1	Book Swap Tea Party	I will join it.	done	2013-10-09 12:21:30	t	done	12	10.00	f	9.00	220.00	DOLLAR	t
13	3	1	1	Buy my old bike.	I am interested	done	2013-10-23 05:01:09	t	done	\N	0.00	f	0.00	0.00	CURRENCY	t
26	2	\N	5	Bhumi wants to add you to as neighbor	You want to add Betty to your neighbours list	pending	2013-12-04 06:57:50	f	pending	\N	\N	f	\N	\N	\N	t
35	2	1792	15	We have car in group.	Hi,,,	pending	2013-12-07 08:15:54	t	pending	\N	0.00	f	0.00	0.00	\N	f
27	2	4	1	Book Swap Tea Party	11 spots i want	pending	2013-12-05 09:36:46	t	pending	11	15.00	f	15.00	165.00	EURO	t
2	2	1	1	Buy my old bike.	I want to buy	done	2013-10-10 12:21:30	t	done	\N	\N	\N	\N	\N	\N	t
36	1	1735	2	Goods For farming	I want 1 piece	pending	2013-12-09 10:18:50	t	pending	1	5.00	f	5.00	5.00	EURO	f
15	1	940	2	Car to use	I will give my bike to you for one day.	pending	2013-11-21 06:55:39	t	pending	\N	0.00	f	0.00	0.00	\N	t
16	1	940	2	Car to use	I will give my bike to you for one day.	done	2013-11-21 06:55:39	t	done	\N	0.00	f	0.00	0.00	\N	t
3	1	540	2	Computer to use	I WANT TO USE YOUR CAR.	accepted	2013-10-15 09:23:55	t	accepted	5	11.00	f	11.00	55.00	DOLLAR	t
4	5	540	2	Computer to use	I WANT TO USE YOUR CAR.	accepted	2013-10-15 09:23:55	t	accepted	10	11.00	f	11.00	110.00	DOLLAR	t
5	8	540	2	Computer to use	I WANT TO USE YOUR CAR.	pending	2013-10-15 09:23:55	t	rejected	5	11.00	f	11.00	55.00	DOLLAR	t
\.


--
-- Name: notice__requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__requests_id_seq', 36, true);


--
-- Data for Name: notice__reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__reviews (id, author_id, notice_id, user_id, text, created_at, title, is_read, type) FROM stdin;
101	3	1	1	I also like your idea	2013-09-10 09:42:34	Sewing Classes @ Sewing Cafe	t	1
103	2	3	1	great	2013-09-10 09:42:34	Festsall Kreuzberg	t	1
104	2	4	1	Nice thinking. I will join it	2013-09-10 09:42:34	Festsall Kreuzberg	t	1
105	1	540	2	You can take my system to use for a day.	2013-09-11 09:42:34	Computer	t	1
3	2	2	1	We should organize this type of event often.	2013-10-04 10:30:05	Monday Mothers Meetup	f	1
102	2	2	1	Yes, I am agree with this.	2013-09-10 09:42:34	Festsall Kreuzberg	t	1
6	1	1342	1	2 days to go..	2013-10-11 05:59:17	Navaratri festival 2013	f	1
7	1	1337	1	Yes, you r right.	2013-10-11 06:07:29	She is very helpfully	f	1
8	2	1	1	I want to see your bike.	2013-10-14 05:29:15	Buy my old bike.	f	1
9	1	1342	1	Finished. Had you enjoyed?	2013-10-15 12:36:48	Navaratri festival 2013	f	1
10	1	1342	1	hi..	2013-10-16 05:45:56	Navaratri festival 2013	f	1
106	2	1	1	I like this.	2013-09-10 09:42:34	Sewing Classes @ Sewing Cafe	t	1
11	2	940	2	Done	2013-10-22 14:32:16	Car to use	f	1
12	2	940	2	Ya sure	2013-10-22 14:33:20	Car to use	f	1
13	2	940	2	How was your experience	2013-10-22 14:34:51	Car to use	f	1
14	2	940	2	How you filled give me response	2013-11-19 10:17:38	Car to use	f	1
15	2	940	2	How was your experience 	2013-12-09 07:29:12	Car to use	f	1
16	2	940	2	ssssssss	2013-12-09 07:44:31	Car to use	f	1
17	2	940	2	xxxxxxxxx	2013-12-09 07:46:34	Car to use	f	1
18	2	2	1	nice 	2013-12-09 07:47:29	Monday Mothers Meetup	f	1
19	2	2	1	ddddd	2013-12-09 08:34:31	Monday Mothers Meetup	f	1
20	2	2	1	great	2013-12-09 08:38:35	Monday Mothers Meetup	f	1
21	2	2	1	sssssss	2013-12-09 09:14:50	Monday Mothers Meetup	f	1
22	2	2	1	try	2013-12-09 09:16:14	Monday Mothers Meetup	f	1
23	2	2	1	ddddddd	2013-12-09 09:24:56	Monday Mothers Meetup	f	1
24	2	2	1	wwwwwwwww	2013-12-09 09:29:32	Monday Mothers Meetup	f	1
25	2	2	1	ccccccccc	2013-12-09 09:31:29	Monday Mothers Meetup	f	1
26	2	2	1	cccccccccc	2013-12-09 09:56:57	Monday Mothers Meetup	f	1
27	1	940	2	bhumi	2013-12-09 10:11:40	Car to use	f	1
28	1	2	1	ok.....	2013-12-09 10:45:25	Monday Mothers Meetup	f	1
29	1	940	2	subjec	2013-12-09 10:46:17	Car to use	f	1
\.


--
-- Name: notice__reviews_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__reviews_id_seq', 29, true);


--
-- Data for Name: notice__type__properties; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__type__properties (type_id, propertytype_id) FROM stdin;
1	5
4	1
6	5
3	5
7	5
6	3
7	3
2	5
9	5
10	2
12	9
13	5
11	7
5	8
\.


--
-- Data for Name: notice__types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__types (id, parent_id, name, sequence, location_change_available, price_available, spot_available, piece_available, date_available, alttext, orderby) FROM stdin;
8	\N	offergroups	8	f	f	f	f	f	groups	\N
12	\N	offerevents	12	t	t	t	f	t	_events	\N
13	\N	offerothers	13	f	f	f	f	t	_others	\N
1	\N	neighbours	1	f	f	f	f	f	neighbours	7
2	\N	groups	2	f	f	f	f	f	groups	8
3	\N	help	3	t	f	f	f	t	need_help	1
4	\N	goods	4	f	f	f	t	t	need_goods	5
6	\N	events	6	t	f	f	f	t	_events	9
7	\N	others	7	f	f	f	f	t	_others	10
9	\N	offerhelp	9	t	f	f	f	t	offer_help	2
10	\N	offergoods	10	f	t	f	t	t	offer_goods	6
11	\N	offerservice	11	f	t	f	f	t	offer_service	4
5	\N	service	5	f	f	f	f	t	demand_service	3
\.


--
-- Name: notice__types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__types_id_seq', 7, true);


--
-- Data for Name: notice__values; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notice__values (id, notice_id, property_id, value) FROM stdin;
76	1310	9	1,2
2	2	2	4
77	1313	9	3,4
4	3	1	4
3	3	3	1
1	1	2	3
6	4	3	2
78	1333	9	4
79	1334	5	1
80	1334	3	2
81	1337	5	1
82	1341	7	2
83	1342	9	4
7	4	9	2
85	1386	3	
84	1386	8	1
86	1434	8	1
87	1496	5	1
67	940	2	3,5
88	1735	2	2,3
89	1783	9	4
90	1792	2	3,5
91	1793	1	4
92	1867	2	4
42	540	2	1,2
43	541	3	2
44	541	1	4
45	544	3	2
46	544	1	3
47	589	5	1
48	589	3	2
65	823	2	2
66	824	2	2,3
73	1247	5	1
\.


--
-- Name: notice__values_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('notice__values_id_seq', 92, true);


--
-- Data for Name: payment__setting; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY payment__setting (id, user_id, account_holder, account_no, bank_code, paypal_email, card_type, card_no, card_holder, end_month, end_year, created_at, agreed, type, cvv_code) FROM stdin;
8	3	\N	\N	\N	bhumi@huskrit.com	\N	34444	bhumi	\N	\N	2013-11-22 11:21:07	t	paypal	\N
10	15	Bhumi112	111111	111111	\N	\N	\N	\N	\N	\N	2013-12-02 10:34:55	t	debit	\N
7	2	Bhumi	1234567890	40050150	\N	\N	\N	\N	\N	\N	2013-10-23 09:52:43	t	debit	\N
\.


--
-- Name: payment__setting_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('payment__setting_id_seq', 10, true);


--
-- Data for Name: share__holder; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY share__holder (id, firstname, surname, city, postalcode, country, birthday, email, share, price, totalamount, terms, street) FROM stdin;
1	bhumi	gothi	Ahemadabad	2222	India	2011-11-11	bhumi@huskerit.com	0	11.00	0.00	t	\N
2	bhumi	gothi	Ahemadabad	2222	India	2011-11-11	bhumi@huskerit.com	1001	11.00	11011.00	t	\N
3	bhumi	gothi	Upleta	362650	India	1989-03-05	bhumi@huskerit.com	11	50.00	550.00	t	rajmarg
4	Hardik	bhatt	Ahemadabad	2222	India	1983-11-29	hardik1@huskerit.com	10	50.00	500.00	t	test
5	Hardik	bhatt	Ahemadabad	211111	India	1983-11-29	hardik@huskerit.com	10	50.00	500.00	t	test
6	Hardik	bhatt	Ahemadabad	211111	India	1983-11-29	hardik@huskerit.com	11	50.00	550.00	t	rajmarg
7	bhumi	gothi	Ahemadabad	211111	India	1988-11-08	bhumi@huskerit.com	11	50.00	550.00	t	rajmarg
8	bhumi	gothi	Ahemadabad	2222	India	1983-11-29	bhumi@huskerit.com	2	50.00	100.00	t	test
9	bhumi	gothi	Ahemadabad	211111	India	1983-11-29	bhumi@huskerit.com	2	50.00	100.00	t	rajmarg
\.


--
-- Name: share__holder_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('share__holder_id_seq', 9, true);


--
-- Data for Name: spatial_ref_sys; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY spatial_ref_sys (srid, auth_name, auth_srid, srtext, proj4text) FROM stdin;
\.


--
-- Data for Name: stickers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY stickers (id, user_id, notice_id, review_id, reported_by_id, reason, description, created_at, discarded_at, comment_id) FROM stdin;
1	\N	4	\N	2	a:0:{}	\N	2013-10-03 13:15:28	\N	\N
2	\N	4	\N	2	a:1:{i:0;i:2;}	\N	2013-11-11 10:48:36	\N	\N
3	\N	4	\N	2	a:2:{i:0;i:5;i:1;i:6;}	\N	2013-11-11 11:14:26	\N	\N
4	\N	4	\N	2	a:2:{i:0;i:3;i:1;i:6;}	\N	2013-11-11 11:26:37	\N	\N
5	\N	4	\N	2	a:1:{i:0;i:6;}	\N	2013-11-11 13:05:39	\N	\N
6	\N	4	\N	2	a:2:{i:0;i:0;i:1;i:3;}	\N	2013-11-11 13:18:01	\N	\N
7	\N	4	\N	2	a:2:{i:0;i:0;i:1;i:4;}	\N	2013-11-11 13:18:42	\N	\N
8	\N	4	\N	2	a:2:{i:0;i:1;i:1;i:4;}	\N	2013-11-11 13:27:46	\N	\N
9	\N	2	\N	2	a:0:{}	\N	2013-11-12 05:26:55	\N	\N
10	\N	2	\N	2	a:2:{i:0;i:1;i:1;i:6;}	\N	2013-11-12 05:29:33	\N	\N
11	\N	2	\N	2	a:2:{i:0;i:1;i:1;i:6;}	\N	2013-11-12 05:56:19	\N	\N
12	\N	2	\N	2	a:0:{}	\N	2013-11-12 06:04:43	\N	\N
13	\N	2	\N	2	a:0:{}	\N	2013-11-12 06:05:44	\N	\N
14	\N	2	\N	2	a:2:{i:0;i:4;i:1;i:5;}	\N	2013-11-12 06:17:46	\N	\N
15	\N	1	\N	2	a:2:{i:0;i:1;i:1;i:5;}	\N	2013-11-12 06:35:11	\N	\N
16	1	\N	\N	2	a:1:{i:0;i:1;}	\N	2013-11-12 07:18:12	\N	\N
17	1	\N	\N	2	a:1:{i:0;i:4;}	\N	2013-11-12 07:21:57	\N	\N
18	\N	1	\N	2	a:2:{i:0;i:3;i:1;i:4;}	\N	2013-11-12 07:22:43	\N	\N
\.


--
-- Name: stickers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('stickers_id_seq', 18, true);


--
-- Data for Name: user__email_change_request; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__email_change_request (id, user_id, requested_email, created_at, confirmation_token) FROM stdin;
\.


--
-- Name: user__email_change_request_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__email_change_request_id_seq', 1, false);


--
-- Data for Name: user__friends; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__friends (user_regular_id, friend_user_regular_id) FROM stdin;
3	1
1	3
\.


--
-- Data for Name: user__friends_invitations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__friends_invitations (id, invitation_by, invitation_for, created_at, updated_at, accepted) FROM stdin;
1	1	3	2013-09-02 18:51:57	2013-10-07 05:24:38	t
\.


--
-- Name: user__friends_invitations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__friends_invitations_id_seq', 1, true);


--
-- Data for Name: user__group; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__group (id, user_id, groupname, about_group, status, path, path2, created_at, locsave) FROM stdin;
1	15	Navratri group	This is our company's navaratri group.	1	1385978700_navaratri1.jpg	1385978700_navaratri.jpg	2013-12-02 10:05:00	\N
2	2	Multicore employess	Multicore employess	1	1386160774_multicore.jpg	\N	2013-12-04 12:39:34	\N
\.


--
-- Name: user__group_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__group_id_seq', 2, true);


--
-- Data for Name: user__neighbors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__neighbors (id, current_id, neighbor_id, created_at) FROM stdin;
10	5	2	2013-12-04 06:57:50
\.


--
-- Name: user__neighbors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__neighbors_id_seq', 9, true);


--
-- Data for Name: user__notification_group_intervals; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__notification_group_intervals (id, notification_group_id, user_id, "interval") FROM stdin;
1	1	1	1
4	1	4	1
6	1	6	1
5	1	5	1
3	1	3	1
9	1	8	1
10	1	9	1
11	1	10	1
12	1	11	1
13	1	12	1
14	1	13	1
15	1	14	1
16	1	15	1
17	1	16	1
2	1	2	1
\.


--
-- Name: user__notification_group_intervals_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__notification_group_intervals_id_seq', 17, true);


--
-- Data for Name: user__notification_groups; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__notification_groups (id, name) FROM stdin;
1	interval
\.


--
-- Name: user__notification_groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__notification_groups_id_seq', 1, true);


--
-- Data for Name: user__notification_queue; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__notification_queue (id, send_after, from_address, from_name, to_address, subject, body_html, body_plain) FROM stdin;
\.


--
-- Name: user__notification_queue_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__notification_queue_id_seq', 1, false);


--
-- Data for Name: user__notifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__notifications (id, notification_group_id, name, description) FROM stdin;
1	1	message	emails_when_someone_sends_you_a_message
3	1	contact_request	emails_when_someone_sends_you_a_contact_request
4	1	reference	emails_when_someone_writes_a_reference_about_you
2	1	request	emails_when_someone_sends_you_a_request
\.


--
-- Name: user__notifications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__notifications_id_seq', 4, true);


--
-- Data for Name: user__references; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__references (id, ref_user_id, new_user_id, new_user_fb_id, new_user_email, active, created_at, activated_at, joined_at) FROM stdin;
\.


--
-- Name: user__references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__references_id_seq', 1, false);


--
-- Data for Name: user__regular; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__regular (id, user_id, firstname, lastname, gender, birthday, about_me, link, facebook_publish, pref_locale, languages, mylike, hotplaces, promotedinitiatives, notshowninfo, postcode) FROM stdin;
2	3	Volker	Volker	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
4	4	James	DeBlasse	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
6	6	Nirav	\N	1	1989-01-01	I am From Ahmedabad	\N	\N	\N	English	Football	Las Vegas	\N	Lastname	\N
5	5	Betty	\N	2	1991-01-01	About me	\N	\N	\N	English	CRICKET	ITALY	\N	LASTNAME	\N
8	8	Bhumi	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
9	9	bhoomi	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
10	10	Hardik	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
11	11	bhumi	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	333333
12	12	bhumi	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	212211
13	13	bhumi	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	3238
16	16	test	\N	\N	\N	\N	\N	\N	en	\N	\N	\N	\N	\N	23898
15	15	test	\N	\N	\N	I like Testing :)	\N	\N	en	\N	\N	\N	\N	\N	11111
14	14	janki	\N	\N	\N	\N	\N	\N	en	\N	\N	\N	\N	\N	222222
3	2	Bhumi	\N	2	1987-08-13	I like ridding	\N	\N	en	\N	Swimming	Garden	\N	firstname	\N
1	1	Jignesh	Vagh	1	1991-01-01	I am from Berlin	\N	\N	en	English,German	Football,Italy	TACABRON,boxhagener	\N	Firstname,Birthdate	\N
\.


--
-- Name: user__regular_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__regular_id_seq', 16, true);


--
-- Data for Name: user__unsubscribers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__unsubscribers (id, hashed_email) FROM stdin;
\.


--
-- Name: user__unsubscribers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__unsubscribers_id_seq', 1, false);


--
-- Data for Name: user__users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__users (id, username, username_canonical, email, email_canonical, enabled, salt, password, last_login, locked, expired, expires_at, confirmation_token, password_requested_at, roles, credentials_expired, credentials_expire_at, facebookid, twitterid, twitter_username, activity, ask_again, registeredwith, registeredwithid, is_business, created_at, managertype, typesid, member) FROM stdin;
4	jdeblasse	jdeblasse	jdeblasse@fenchy.com	jdeblasse@fenchy.com	t	qc1b5djna8g8sw0cww08csc0ow80ss0	fIXnmB7OX7Q7ZIjYGzSHB1j6RI916hS1cjKwL2etb/YykAF61IhljDGduJQ+s5iXbnrhdd4pNRbKbqHf4f1G6g==	\N	f	f	\N	\N	\N	a:1:{i:0;s:10:"ROLE_ADMIN";}	f	\N	\N	\N	\N	5	t	email	jdeblasse@fenchy.com	f	2013-08-09 16:54:25	\N	\N	\N
6	52245ae7f3a0f	52245ae7f3a0f	nirav.jogiya@huskerit.com	nirav.jogiya@huskerit.com	t	cokjwbvmqz48wssw48k08w48w04k808	yLJB0aUO4YW2Kz/hZv+MEP9gQnoJpxbzaIHP+tlzTVsRvCLGSDwRjTyxOTEghMKvryVe07TzmtqkSVc2izsSNA==	2013-09-02 19:11:57	f	f	\N	5fvvkpnrmds8wsgog80o0sog800k0c4wwkkcsogs48oo4wss0k	\N	a:0:{}	f	\N	\N	\N	images/default_profile_picture.png	20	t	email	nirav.jogiya@huskerit.com	f	2013-09-02 15:01:19	\N	\N	\N
9	5270e54e9cda2	5270e54e9cda2	nidhi1@huskerit.com	nidhi@huskerit.com	t	3xtznnaktugw8s0oogw4c480ko00ckc	+NkuulDLFS+GghiUbiPqFzHqc8BKcW6oJODdDWEBtU0SMjcXXLBmGdXVHyGZgCBgozDAOPiVU5n3ZElue8+9aw==	\N	f	f	\N	1zd63p7frdus4s40c088ososow4s0kgcw484cgks4wkc8wssw4	\N	a:0:{}	f	\N	\N	\N	\N	0	t	email	nidhi@huskerit.com	f	2013-10-30 10:54:06	\N	\N	\N
11	5273782b64871	5273782b64871	Bhumibhalodiya1@gmail.com	bhumibhalodiya1@gmail.com	t	abw0v6tpf0w8gc444c4woowss0o4skg	AI+eMlAlenVS4p13xojepFafoqYGTmKtcxIEjzftdbZjF9GrrlxxXKINM3bj3HqImfOVj6j3L5oaXXRY0rt9DQ==	2013-11-01 09:49:26	f	f	\N	3j973e255rs4okkkcoskcco08ogck8888k4gc4sckscososo80	\N	a:0:{}	f	\N	\N	\N	\N	0	t	email	Bhumibhalodiya1@gmail.com	f	2013-11-01 09:45:15	\N	\N	\N
12	5273853b9bf44	5273853b9bf44	bhumi1@huskerit.com	bhumi1@huskerit.com	t	f99ztklaj88cwwcwk0w4488080cwc00	ljUKIf3/5QI5rSmYbjK34vB0rbWLYne2sqitQO+PA6Zoq+wjZeAPDA43Tj6ZUfOFWYKld0iArRvEjKtvkqEovw==	\N	f	f	\N	2ozabnny4tgk040w8cskc8ccg4sc4440448ok4k4c4wok848s8	\N	a:0:{}	f	\N	\N	\N	\N	0	t	email	bhumi1@huskerit.com	f	2013-11-01 10:40:59	\N	\N	\N
13	527385daef880	527385daef880	bhumi3@huskerit.com	bhumi3@huskerit.com	t	46xabahpqaqs4wskw8gw8gg0g4koog0	+1Mp0IZWdthihLyuNklzh7F5LZ127QBLkshlKrmCt6PQNhRrAsyLB5YGYc504COljUgMtiJcnSnsWTMe4Z7oGg==	\N	f	f	\N	5i0087uveococw848844okcsk80k8s0w0wc0gwg04gw4w0wokg	\N	a:0:{}	f	\N	\N	\N	\N	0	t	email	bhumi3@huskerit.com	f	2013-11-01 10:43:38	\N	\N	\N
2	sakstinat	sakstinat	bhumi@huskerit.com	bhumi@huskerit.com	t	pjrm4wy4suss4w84444k0k0wo8c00og	28akZLCJ9pagHgxt4sjG9mfOoGzZ+Lr7vGM2OwKFLxsNU9FAys/NPOWHtzX9Q7kGnky2mpsF3NYAN2PYrnWbGw==	2013-12-10 05:26:12	f	f	\N	4firmdh5sq68sg40cokgookcws8kos040k880c8sgo0kko840	2013-11-29 07:18:19	a:1:{i:0;s:10:"ROLE_ADMIN";}	f	\N	\N	\N	uploads/profileimages/1383214907_startrail-panorama-Vincent-Brady.jpg	212	t	email	sakstinat@fenchy.com	f	2013-08-09 16:54:25	\N	\N	t
16	5289ffdc66bca	5289ffdc66bca	test@huskerit.com	test@huskerit.com	t	lz8tb6bg7dccsw84o0g0gocs0ksgkwg	gsYuubGYoEGgGA3fcv5tS1Um9l2VnAGB0T3eNKILyPwwvvcnfVyJEwegnExrbmNba+4TANCp77S/oJPkMs2w5Q==	\N	f	f	\N	1zlxtlpkunfo0oggckcw8gow4ks8ssoko8cgsgkoscs8gk8o8k	\N	a:0:{}	f	\N	\N	\N	\N	0	t	email	test@huskerit.com	f	2013-11-18 11:54:04	\N	\N	\N
3	vsiems	vsiems	vsiems@fenchy.com	vsiems@fenchy.com	t	8pt9wwy9r74sw0cc08wok8cg4s4k0ks	PwtabIu16dhDE8FG0kus2SOTUY8ylZIS6uUPV5Ur/uHxS25PE8VDEm51klulLuLJaNJRruib02xsx0k7azFKqA==	\N	f	f	\N	\N	\N	a:1:{i:0;s:10:"ROLE_ADMIN";}	f	\N	\N	\N	\N	6	t	email	vsiems@fenchy.com	f	2013-08-09 16:54:25	\N	\N	\N
15	5274c705497af	5274c705497af	bhumi122@huskerit.com	bhumi122@huskerit.com	t	34emslk5xhq8kwg0cwo4k4s4ssg48ck	O2KyhgS3g4Ng499DMGauZxvstQfx53KQL3R92EyaZsFXKYqsAi4FAnLxRbd0yDwPRSvr3XgSimAqrzVAtQvGvg==	2013-12-07 08:39:02	f	f	\N	49cqsjxq254ws8o0ogcgkcokg8g0w40wg440k8oco0wkkwk84o	\N	a:0:{}	f	\N	\N	\N	images/default_profile_picture.png	11	t	email	bhumi122@huskerit.com	f	2013-11-02 09:33:57	\N	\N	\N
8	5270e0b3cb118	5270e0b3cb118	bhumibhalodiya@gmail.com	bhumibhalodiya@gmail.com	t	hrpm6hi2ajw4gkkwg4k8ogss00cccwk	TxaftL3bKh+syeG+3+At+DwW4abkG+aPB6YjAj/RaIOEpMnBEQBu+ZXmcjbL9etF8H8SWieNC8H7lJiB4l/SsQ==	2013-11-26 06:12:33	f	f	\N	4hthaxk8auck00g8kggks44840s4kg8k4soosccc44woog4ogg	\N	a:0:{}	f	\N	\N	\N	images/default_profile_picture.png	0	t	email	bhumibhalodiya@gmail.com	f	2013-10-30 10:34:27	\N	\N	\N
10	5270f3bd39e4f	5270f3bd39e4f	hardik@huskerit.com	hardik@huskerit.com	t	h1lfz5unl7w4c08osw4kwoooc0408g8	R2Yx8QfMzsFpOM/RgTpBRdP8B9rneZKy+8OFjoUld6xJg+ETjnLemHHIh7+RnhtJrlEevBLqV/fjDlMsie4q2g==	2013-12-03 06:02:52	f	f	\N	4a2o7k5u6ruosswc48o0sckg0csskco0ow8g4sgc484w4kgoco	\N	a:0:{}	f	\N	\N	\N	uploads/profileimages/1384947431_IMG_6467.JPG	27	t	email	hardik@huskerit.com	f	2013-10-30 11:55:41	\N	\N	t
1	meisele	meisele	jignesh.vagh@huskerit.com	jignesh.vagh@huskerit.com	t	4xt2dyyv0qkgc8cc0sk40wwwow084k4	yoY9czIbCEPi6MTPwlikWTvlAMD9iRnltLhO1sAtaYv16HsT5v9NqQVralcXm9hFAGB0+Z5JnWnyMrkoR05ZpA==	2013-12-09 10:44:34	f	f	\N	\N	\N	a:1:{i:0;s:10:"ROLE_ADMIN";}	f	\N	\N	\N	\N	60	t	email	matthias@fenchy.com	f	2013-08-09 16:54:24	f	\N	\N
5	5204da6b6e002	5204da6b6e002	jignesh@huskerit.com	jignesh@huskerit.com	t	12nzxt94dzrkc8480kog84w40c8gs84	VZTfp4r2Dq70b1RgirxwbClpiZvANRLLOZatiSNC2+I6xibYjG16ZfyAr+bX/hnkRtPNqyThy+BsvXbmPFr5/w==	2013-12-06 11:24:27	f	f	\N	4g1byti4nywwsgcwo844g4sso0o0k4owgswss48ss4gw4084cs	\N	a:0:{}	f	\N	\N	\N	images/default_profile_picture.png	20	t	email	jignesh.vagh@huskerit.com	f	2013-08-09 17:32:51	\N	\N	\N
14	52738b3003647	52738b3003647	janki@huskerit.com	janki@huskerit.com	t	4u9lz2xj05uss88o0kwcgggco0kos4c	J1BqdLR+VWS89wlGOXVUEqD/FgT35xxLDCrYt/Q07JAIeBwcNzGFX56NK2vfTAf0k2ys9IjwEnUI7E3PMMg+CA==	2013-12-09 04:49:03	f	f	\N	1qps5098zksg00kookcg0ssc4occskks0okc0go84s8008k0kg	\N	a:0:{}	f	\N	\N	\N	\N	0	t	email	janki@huskerit.com	f	2013-11-01 11:06:24	\N	\N	\N
\.


--
-- Data for Name: user__users__notifications; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY user__users__notifications (user_id, notification_id) FROM stdin;
2	2
2	1
2	3
\.


--
-- Name: user__users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('user__users_id_seq', 16, true);


--
-- Data for Name: usergroup__members; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY usergroup__members (id, usergroup_id, member_id, admin, created_at) FROM stdin;
\.


--
-- Name: usergroup__members_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('usergroup__members_id_seq', 1, false);


--
-- Name: document_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY document
    ADD CONSTRAINT document_pkey PRIMARY KEY (id);


--
-- Name: ext_log_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ext_log_entries
    ADD CONSTRAINT ext_log_entries_pkey PRIMARY KEY (id);


--
-- Name: ext_translations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ext_translations
    ADD CONSTRAINT ext_translations_pkey PRIMARY KEY (id);


--
-- Name: galleries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY galleries
    ADD CONSTRAINT galleries_pkey PRIMARY KEY (id);


--
-- Name: identity__verification_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY identity__verification
    ADD CONSTRAINT identity__verification_pkey PRIMARY KEY (id);


--
-- Name: images_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY images
    ADD CONSTRAINT images_pkey PRIMARY KEY (id);


--
-- Name: location__verification_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY location__verification
    ADD CONSTRAINT location__verification_pkey PRIMARY KEY (id);


--
-- Name: location_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY location
    ADD CONSTRAINT location_pkey PRIMARY KEY (id);


--
-- Name: message__about_notice_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY message__about_notice
    ADD CONSTRAINT message__about_notice_pkey PRIMARY KEY (message_id, about_notice_id);


--
-- Name: message__about_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY message__about_user
    ADD CONSTRAINT message__about_user_pkey PRIMARY KEY (message_id, about_user_id);


--
-- Name: message__messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT message__messages_pkey PRIMARY KEY (id);


--
-- Name: notice__comments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__comments
    ADD CONSTRAINT notice__comments_pkey PRIMARY KEY (id);


--
-- Name: notice__dictionary_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__dictionary
    ADD CONSTRAINT notice__dictionary_pkey PRIMARY KEY (id);


--
-- Name: notice__facebook_feed_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__facebook_feed
    ADD CONSTRAINT notice__facebook_feed_pkey PRIMARY KEY (id);


--
-- Name: notice__notices__dictionaries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__notices__dictionaries
    ADD CONSTRAINT notice__notices__dictionaries_pkey PRIMARY KEY (dictionary_id, notice_id);


--
-- Name: notice__notices_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__notices
    ADD CONSTRAINT notice__notices_pkey PRIMARY KEY (id);


--
-- Name: notice__property_types_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__property_types
    ADD CONSTRAINT notice__property_types_pkey PRIMARY KEY (id);


--
-- Name: notice__requests_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__requests
    ADD CONSTRAINT notice__requests_pkey PRIMARY KEY (id);


--
-- Name: notice__reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__reviews
    ADD CONSTRAINT notice__reviews_pkey PRIMARY KEY (id);


--
-- Name: notice__type__properties_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__type__properties
    ADD CONSTRAINT notice__type__properties_pkey PRIMARY KEY (type_id, propertytype_id);


--
-- Name: notice__types_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__types
    ADD CONSTRAINT notice__types_pkey PRIMARY KEY (id);


--
-- Name: notice__values_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notice__values
    ADD CONSTRAINT notice__values_pkey PRIMARY KEY (id);


--
-- Name: payment__setting_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY payment__setting
    ADD CONSTRAINT payment__setting_pkey PRIMARY KEY (id);


--
-- Name: share__holder_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY share__holder
    ADD CONSTRAINT share__holder_pkey PRIMARY KEY (id);


--
-- Name: spatial_ref_sys_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY spatial_ref_sys
    ADD CONSTRAINT spatial_ref_sys_pkey PRIMARY KEY (srid);


--
-- Name: stickers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY stickers
    ADD CONSTRAINT stickers_pkey PRIMARY KEY (id);


--
-- Name: user__email_change_request_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__email_change_request
    ADD CONSTRAINT user__email_change_request_pkey PRIMARY KEY (id);


--
-- Name: user__friends_invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__friends_invitations
    ADD CONSTRAINT user__friends_invitations_pkey PRIMARY KEY (id);


--
-- Name: user__friends_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__friends
    ADD CONSTRAINT user__friends_pkey PRIMARY KEY (user_regular_id, friend_user_regular_id);


--
-- Name: user__group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__group
    ADD CONSTRAINT user__group_pkey PRIMARY KEY (id);


--
-- Name: user__neighbors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__neighbors
    ADD CONSTRAINT user__neighbors_pkey PRIMARY KEY (id);


--
-- Name: user__notification_group_intervals_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__notification_group_intervals
    ADD CONSTRAINT user__notification_group_intervals_pkey PRIMARY KEY (id);


--
-- Name: user__notification_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__notification_groups
    ADD CONSTRAINT user__notification_groups_pkey PRIMARY KEY (id);


--
-- Name: user__notification_queue_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__notification_queue
    ADD CONSTRAINT user__notification_queue_pkey PRIMARY KEY (id);


--
-- Name: user__notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__notifications
    ADD CONSTRAINT user__notifications_pkey PRIMARY KEY (id);


--
-- Name: user__references_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__references
    ADD CONSTRAINT user__references_pkey PRIMARY KEY (id);


--
-- Name: user__regular_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__regular
    ADD CONSTRAINT user__regular_pkey PRIMARY KEY (id);


--
-- Name: user__unsubscribers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__unsubscribers
    ADD CONSTRAINT user__unsubscribers_pkey PRIMARY KEY (id);


--
-- Name: user__users__notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__users__notifications
    ADD CONSTRAINT user__users__notifications_pkey PRIMARY KEY (user_id, notification_id);


--
-- Name: user__users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY user__users
    ADD CONSTRAINT user__users_pkey PRIMARY KEY (id);


--
-- Name: usergroup__members_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY usergroup__members
    ADD CONSTRAINT usergroup__members_pkey PRIMARY KEY (id);


--
-- Name: idx_1c7784377d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_1c7784377d540ab ON notice__requests USING btree (notice_id);


--
-- Name: idx_1c778437a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_1c778437a76ed395 ON notice__requests USING btree (user_id);


--
-- Name: idx_1c778437f675f31b; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_1c778437f675f31b ON notice__requests USING btree (author_id);


--
-- Name: idx_386cc44c7d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_386cc44c7d540ab ON notice__comments USING btree (notice_id);


--
-- Name: idx_386cc44ca76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_386cc44ca76ed395 ON notice__comments USING btree (user_id);


--
-- Name: idx_386cc44cd2112630; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_386cc44cd2112630 ON notice__comments USING btree (usergroup_id);


--
-- Name: idx_386cc44cf675f31b; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_386cc44cf675f31b ON notice__comments USING btree (author_id);


--
-- Name: idx_4134cb49a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4134cb49a76ed395 ON notice__notices USING btree (user_id);


--
-- Name: idx_4134cb49c54c8c93; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4134cb49c54c8c93 ON notice__notices USING btree (type_id);


--
-- Name: idx_4134cb49d2112630; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4134cb49d2112630 ON notice__notices USING btree (usergroup_id);


--
-- Name: idx_466841947d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_466841947d540ab ON notice__reviews USING btree (notice_id);


--
-- Name: idx_46684194a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_46684194a76ed395 ON notice__reviews USING btree (user_id);


--
-- Name: idx_46684194f675f31b; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_46684194f675f31b ON notice__reviews USING btree (author_id);


--
-- Name: idx_48bff79723afcfe2; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_48bff79723afcfe2 ON user__friends_invitations USING btree (invitation_by);


--
-- Name: idx_48bff79739bfce0d; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_48bff79739bfce0d ON user__friends_invitations USING btree (invitation_for);


--
-- Name: idx_4d7150da549213ec; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4d7150da549213ec ON notice__values USING btree (property_id);


--
-- Name: idx_4d7150da7d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4d7150da7d540ab ON notice__values USING btree (notice_id);


--
-- Name: idx_4dfcd61233f78756; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4dfcd61233f78756 ON message__about_notice USING btree (about_notice_id);


--
-- Name: idx_4dfcd612537a1329; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_4dfcd612537a1329 ON message__about_notice USING btree (message_id);


--
-- Name: idx_554e1471637a8045; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_554e1471637a8045 ON user__references USING btree (ref_user_id);


--
-- Name: idx_554e14717c2d807b; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_554e14717c2d807b ON user__references USING btree (new_user_id);


--
-- Name: idx_790692837597d3fe; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_790692837597d3fe ON usergroup__members USING btree (member_id);


--
-- Name: idx_79069283d2112630; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_79069283d2112630 ON usergroup__members USING btree (usergroup_id);


--
-- Name: idx_7e1754c34208046c; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_7e1754c34208046c ON notice__type__properties USING btree (propertytype_id);


--
-- Name: idx_7e1754c3c54c8c93; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_7e1754c3c54c8c93 ON notice__type__properties USING btree (type_id);


--
-- Name: idx_7fe46ee57d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_7fe46ee57d540ab ON notice__notices__dictionaries USING btree (notice_id);


--
-- Name: idx_7fe46ee5af5e5b3c; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_7fe46ee5af5e5b3c ON notice__notices__dictionaries USING btree (dictionary_id);


--
-- Name: idx_7ffeaff2a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_7ffeaff2a76ed395 ON user__notification_group_intervals USING btree (user_id);


--
-- Name: idx_7ffeaff2ab44e1e2; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_7ffeaff2ab44e1e2 ON user__notification_group_intervals USING btree (notification_group_id);


--
-- Name: idx_82aab364a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_82aab364a76ed395 ON user__group USING btree (user_id);


--
-- Name: idx_881c2331727aca70; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_881c2331727aca70 ON notice__types USING btree (parent_id);


--
-- Name: idx_9db25398537a1329; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_9db25398537a1329 ON message__about_user USING btree (message_id);


--
-- Name: idx_9db25398d07fe4b4; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_9db25398d07fe4b4 ON message__about_user USING btree (about_user_id);


--
-- Name: idx_9f7b5dc02a98155e; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_9f7b5dc02a98155e ON message__messages USING btree (sender_user_id);


--
-- Name: idx_9f7b5dc0c2e2722e; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_9f7b5dc0c2e2722e ON message__messages USING btree (first_message_id);


--
-- Name: idx_9f7b5dc0d2112630; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_9f7b5dc0d2112630 ON message__messages USING btree (usergroup_id);


--
-- Name: idx_9f7b5dc0da57e237; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_9f7b5dc0da57e237 ON message__messages USING btree (receiver_user_id);


--
-- Name: idx_a4640589ca3465c1; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_a4640589ca3465c1 ON user__neighbors USING btree (neighbor_id);


--
-- Name: idx_a4640589f58a7a5c; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_a4640589f58a7a5c ON user__neighbors USING btree (current_id);


--
-- Name: idx_a4d52fa4ab44e1e2; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_a4d52fa4ab44e1e2 ON user__notifications USING btree (notification_group_id);


--
-- Name: idx_b52b751447898c13; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_b52b751447898c13 ON user__friends USING btree (user_regular_id);


--
-- Name: idx_b52b7514fe5ca101; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_b52b7514fe5ca101 ON user__friends USING btree (friend_user_regular_id);


--
-- Name: idx_cc2daa03a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_cc2daa03a76ed395 ON user__users__notifications USING btree (user_id);


--
-- Name: idx_cc2daa03ef1a9d84; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_cc2daa03ef1a9d84 ON user__users__notifications USING btree (notification_id);


--
-- Name: idx_d88dac163e2e969b; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_d88dac163e2e969b ON stickers USING btree (review_id);


--
-- Name: idx_d88dac1671ce806; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_d88dac1671ce806 ON stickers USING btree (reported_by_id);


--
-- Name: idx_d88dac167d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_d88dac167d540ab ON stickers USING btree (notice_id);


--
-- Name: idx_d88dac16a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_d88dac16a76ed395 ON stickers USING btree (user_id);


--
-- Name: idx_d88dac16f8697d13; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_d88dac16f8697d13 ON stickers USING btree (comment_id);


--
-- Name: idx_db33587c7d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_db33587c7d540ab ON notice__facebook_feed USING btree (notice_id);


--
-- Name: idx_e01fbe6a4e7af8f; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_e01fbe6a4e7af8f ON images USING btree (gallery_id);


--
-- Name: log_class_lookup_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX log_class_lookup_idx ON ext_log_entries USING btree (object_class);


--
-- Name: log_date_lookup_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX log_date_lookup_idx ON ext_log_entries USING btree (logged_at);


--
-- Name: log_user_lookup_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX log_user_lookup_idx ON ext_log_entries USING btree (username);


--
-- Name: lookup_unique_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations USING btree (locale, object_class, field, foreign_key);


--
-- Name: propertytypename_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX propertytypename_idx ON notice__property_types USING btree (name);


--
-- Name: translations_lookup_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX translations_lookup_idx ON ext_translations USING btree (locale, object_class, foreign_key);


--
-- Name: typename_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX typename_idx ON notice__types USING btree (name);


--
-- Name: uniq_1b97be95a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_1b97be95a76ed395 ON identity__verification USING btree (user_id);


--
-- Name: uniq_554e1471e7824ece; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_554e1471e7824ece ON user__references USING btree (new_user_fb_id);


--
-- Name: uniq_5e9e89cb7d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_5e9e89cb7d540ab ON location USING btree (notice_id);


--
-- Name: uniq_5e9e89cba76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_5e9e89cba76ed395 ON location USING btree (user_id);


--
-- Name: uniq_5e9e89cbd2112630; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_5e9e89cbd2112630 ON location USING btree (usergroup_id);


--
-- Name: uniq_6eaaf2b4a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_6eaaf2b4a76ed395 ON user__email_change_request USING btree (user_id);


--
-- Name: uniq_9f7b5dc05993b147; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_9f7b5dc05993b147 ON message__messages USING btree (prev_message_id);


--
-- Name: uniq_9f7b5dc0ecedf8ea; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_9f7b5dc0ecedf8ea ON message__messages USING btree (next_message_id);


--
-- Name: uniq_a9627e2d3b8ba7c7; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_a9627e2d3b8ba7c7 ON notice__dictionary USING btree (text);


--
-- Name: uniq_aad14cada76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_aad14cada76ed395 ON location__verification USING btree (user_id);


--
-- Name: uniq_abfa1b8da76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_abfa1b8da76ed395 ON payment__setting USING btree (user_id);


--
-- Name: uniq_deb406a9a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_deb406a9a76ed395 ON user__regular USING btree (user_id);


--
-- Name: uniq_e9530e3458e7f24; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_e9530e3458e7f24 ON user__unsubscribers USING btree (hashed_email);


--
-- Name: uniq_f70e6eb7108b7592; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_f70e6eb7108b7592 ON galleries USING btree (original_id);


--
-- Name: uniq_f70e6eb77d540ab; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_f70e6eb77d540ab ON galleries USING btree (notice_id);


--
-- Name: uniq_f70e6eb7a76ed395; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_f70e6eb7a76ed395 ON galleries USING btree (user_id);


--
-- Name: uniq_f70e6eb7e0ee4aff; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_f70e6eb7e0ee4aff ON galleries USING btree (regular_id);


--
-- Name: uniq_fbe952487ea22896; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_fbe952487ea22896 ON user__users USING btree (managertype);


--
-- Name: uniq_fbe9524892fc23a8; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_fbe9524892fc23a8 ON user__users USING btree (username_canonical);


--
-- Name: uniq_fbe95248a0d96fbf; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX uniq_fbe95248a0d96fbf ON user__users USING btree (email_canonical);


--
-- Name: geometry_columns_delete; Type: RULE; Schema: public; Owner: postgres
--

CREATE RULE geometry_columns_delete AS ON DELETE TO geometry_columns DO INSTEAD NOTHING;


--
-- Name: geometry_columns_insert; Type: RULE; Schema: public; Owner: postgres
--

CREATE RULE geometry_columns_insert AS ON INSERT TO geometry_columns DO INSTEAD NOTHING;


--
-- Name: geometry_columns_update; Type: RULE; Schema: public; Owner: postgres
--

CREATE RULE geometry_columns_update AS ON UPDATE TO geometry_columns DO INSTEAD NOTHING;


--
-- Name: fk_1b97be95a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY identity__verification
    ADD CONSTRAINT fk_1b97be95a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_1c7784377d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__requests
    ADD CONSTRAINT fk_1c7784377d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id) ON DELETE SET NULL;


--
-- Name: fk_1c778437a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__requests
    ADD CONSTRAINT fk_1c778437a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_1c778437f675f31b; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__requests
    ADD CONSTRAINT fk_1c778437f675f31b FOREIGN KEY (author_id) REFERENCES user__users(id);


--
-- Name: fk_386cc44c7d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__comments
    ADD CONSTRAINT fk_386cc44c7d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id) ON DELETE SET NULL;


--
-- Name: fk_386cc44ca76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__comments
    ADD CONSTRAINT fk_386cc44ca76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_386cc44cd2112630; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__comments
    ADD CONSTRAINT fk_386cc44cd2112630 FOREIGN KEY (usergroup_id) REFERENCES user__group(id) ON DELETE SET NULL;


--
-- Name: fk_386cc44cf675f31b; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__comments
    ADD CONSTRAINT fk_386cc44cf675f31b FOREIGN KEY (author_id) REFERENCES user__users(id);


--
-- Name: fk_4134cb49a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__notices
    ADD CONSTRAINT fk_4134cb49a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_4134cb49c54c8c93; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__notices
    ADD CONSTRAINT fk_4134cb49c54c8c93 FOREIGN KEY (type_id) REFERENCES notice__types(id);


--
-- Name: fk_4134cb49d2112630; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__notices
    ADD CONSTRAINT fk_4134cb49d2112630 FOREIGN KEY (usergroup_id) REFERENCES user__group(id);


--
-- Name: fk_466841947d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__reviews
    ADD CONSTRAINT fk_466841947d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id) ON DELETE SET NULL;


--
-- Name: fk_46684194a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__reviews
    ADD CONSTRAINT fk_46684194a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_46684194f675f31b; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__reviews
    ADD CONSTRAINT fk_46684194f675f31b FOREIGN KEY (author_id) REFERENCES user__users(id);


--
-- Name: fk_48bff79723afcfe2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__friends_invitations
    ADD CONSTRAINT fk_48bff79723afcfe2 FOREIGN KEY (invitation_by) REFERENCES user__regular(id);


--
-- Name: fk_48bff79739bfce0d; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__friends_invitations
    ADD CONSTRAINT fk_48bff79739bfce0d FOREIGN KEY (invitation_for) REFERENCES user__regular(id);


--
-- Name: fk_4d7150da549213ec; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__values
    ADD CONSTRAINT fk_4d7150da549213ec FOREIGN KEY (property_id) REFERENCES notice__property_types(id);


--
-- Name: fk_4d7150da7d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__values
    ADD CONSTRAINT fk_4d7150da7d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id);


--
-- Name: fk_4dfcd61233f78756; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__about_notice
    ADD CONSTRAINT fk_4dfcd61233f78756 FOREIGN KEY (about_notice_id) REFERENCES notice__notices(id);


--
-- Name: fk_4dfcd612537a1329; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__about_notice
    ADD CONSTRAINT fk_4dfcd612537a1329 FOREIGN KEY (message_id) REFERENCES message__messages(id);


--
-- Name: fk_554e1471637a8045; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__references
    ADD CONSTRAINT fk_554e1471637a8045 FOREIGN KEY (ref_user_id) REFERENCES user__users(id);


--
-- Name: fk_554e14717c2d807b; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__references
    ADD CONSTRAINT fk_554e14717c2d807b FOREIGN KEY (new_user_id) REFERENCES user__users(id);


--
-- Name: fk_5e9e89cb7d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY location
    ADD CONSTRAINT fk_5e9e89cb7d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id);


--
-- Name: fk_5e9e89cba76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY location
    ADD CONSTRAINT fk_5e9e89cba76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_5e9e89cbd2112630; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY location
    ADD CONSTRAINT fk_5e9e89cbd2112630 FOREIGN KEY (usergroup_id) REFERENCES user__group(id);


--
-- Name: fk_6eaaf2b4a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__email_change_request
    ADD CONSTRAINT fk_6eaaf2b4a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_790692837597d3fe; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY usergroup__members
    ADD CONSTRAINT fk_790692837597d3fe FOREIGN KEY (member_id) REFERENCES user__users(id);


--
-- Name: fk_79069283d2112630; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY usergroup__members
    ADD CONSTRAINT fk_79069283d2112630 FOREIGN KEY (usergroup_id) REFERENCES user__group(id);


--
-- Name: fk_7e1754c34208046c; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__type__properties
    ADD CONSTRAINT fk_7e1754c34208046c FOREIGN KEY (propertytype_id) REFERENCES notice__property_types(id) ON DELETE CASCADE;


--
-- Name: fk_7e1754c3c54c8c93; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__type__properties
    ADD CONSTRAINT fk_7e1754c3c54c8c93 FOREIGN KEY (type_id) REFERENCES notice__types(id) ON DELETE CASCADE;


--
-- Name: fk_7fe46ee57d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__notices__dictionaries
    ADD CONSTRAINT fk_7fe46ee57d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id) ON DELETE CASCADE;


--
-- Name: fk_7fe46ee5af5e5b3c; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__notices__dictionaries
    ADD CONSTRAINT fk_7fe46ee5af5e5b3c FOREIGN KEY (dictionary_id) REFERENCES notice__dictionary(id) ON DELETE CASCADE;


--
-- Name: fk_7ffeaff2a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__notification_group_intervals
    ADD CONSTRAINT fk_7ffeaff2a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_7ffeaff2ab44e1e2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__notification_group_intervals
    ADD CONSTRAINT fk_7ffeaff2ab44e1e2 FOREIGN KEY (notification_group_id) REFERENCES user__notification_groups(id);


--
-- Name: fk_82aab364a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__group
    ADD CONSTRAINT fk_82aab364a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_881c2331727aca70; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__types
    ADD CONSTRAINT fk_881c2331727aca70 FOREIGN KEY (parent_id) REFERENCES notice__types(id);


--
-- Name: fk_9db25398537a1329; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__about_user
    ADD CONSTRAINT fk_9db25398537a1329 FOREIGN KEY (message_id) REFERENCES message__messages(id);


--
-- Name: fk_9db25398d07fe4b4; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__about_user
    ADD CONSTRAINT fk_9db25398d07fe4b4 FOREIGN KEY (about_user_id) REFERENCES user__users(id);


--
-- Name: fk_9f7b5dc02a98155e; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT fk_9f7b5dc02a98155e FOREIGN KEY (sender_user_id) REFERENCES user__users(id);


--
-- Name: fk_9f7b5dc05993b147; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT fk_9f7b5dc05993b147 FOREIGN KEY (prev_message_id) REFERENCES message__messages(id);


--
-- Name: fk_9f7b5dc0c2e2722e; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT fk_9f7b5dc0c2e2722e FOREIGN KEY (first_message_id) REFERENCES message__messages(id);


--
-- Name: fk_9f7b5dc0d2112630; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT fk_9f7b5dc0d2112630 FOREIGN KEY (usergroup_id) REFERENCES user__group(id);


--
-- Name: fk_9f7b5dc0da57e237; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT fk_9f7b5dc0da57e237 FOREIGN KEY (receiver_user_id) REFERENCES user__users(id);


--
-- Name: fk_9f7b5dc0ecedf8ea; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY message__messages
    ADD CONSTRAINT fk_9f7b5dc0ecedf8ea FOREIGN KEY (next_message_id) REFERENCES message__messages(id);


--
-- Name: fk_a4640589ca3465c1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__neighbors
    ADD CONSTRAINT fk_a4640589ca3465c1 FOREIGN KEY (neighbor_id) REFERENCES user__users(id);


--
-- Name: fk_a4640589f58a7a5c; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__neighbors
    ADD CONSTRAINT fk_a4640589f58a7a5c FOREIGN KEY (current_id) REFERENCES user__users(id);


--
-- Name: fk_a4d52fa4ab44e1e2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__notifications
    ADD CONSTRAINT fk_a4d52fa4ab44e1e2 FOREIGN KEY (notification_group_id) REFERENCES user__notification_groups(id);


--
-- Name: fk_aad14cada76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY location__verification
    ADD CONSTRAINT fk_aad14cada76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_abfa1b8da76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY payment__setting
    ADD CONSTRAINT fk_abfa1b8da76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_b52b751447898c13; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__friends
    ADD CONSTRAINT fk_b52b751447898c13 FOREIGN KEY (user_regular_id) REFERENCES user__regular(id);


--
-- Name: fk_b52b7514fe5ca101; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__friends
    ADD CONSTRAINT fk_b52b7514fe5ca101 FOREIGN KEY (friend_user_regular_id) REFERENCES user__regular(id);


--
-- Name: fk_cc2daa03a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__users__notifications
    ADD CONSTRAINT fk_cc2daa03a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id) ON DELETE CASCADE;


--
-- Name: fk_cc2daa03ef1a9d84; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__users__notifications
    ADD CONSTRAINT fk_cc2daa03ef1a9d84 FOREIGN KEY (notification_id) REFERENCES user__notifications(id) ON DELETE CASCADE;


--
-- Name: fk_d88dac163e2e969b; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stickers
    ADD CONSTRAINT fk_d88dac163e2e969b FOREIGN KEY (review_id) REFERENCES notice__reviews(id);


--
-- Name: fk_d88dac1671ce806; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stickers
    ADD CONSTRAINT fk_d88dac1671ce806 FOREIGN KEY (reported_by_id) REFERENCES user__users(id);


--
-- Name: fk_d88dac167d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stickers
    ADD CONSTRAINT fk_d88dac167d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id);


--
-- Name: fk_d88dac16a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stickers
    ADD CONSTRAINT fk_d88dac16a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_d88dac16f8697d13; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY stickers
    ADD CONSTRAINT fk_d88dac16f8697d13 FOREIGN KEY (comment_id) REFERENCES notice__comments(id);


--
-- Name: fk_db33587c7d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notice__facebook_feed
    ADD CONSTRAINT fk_db33587c7d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id);


--
-- Name: fk_deb406a9a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY user__regular
    ADD CONSTRAINT fk_deb406a9a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_e01fbe6a4e7af8f; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY images
    ADD CONSTRAINT fk_e01fbe6a4e7af8f FOREIGN KEY (gallery_id) REFERENCES galleries(id);


--
-- Name: fk_f70e6eb7108b7592; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY galleries
    ADD CONSTRAINT fk_f70e6eb7108b7592 FOREIGN KEY (original_id) REFERENCES galleries(id);


--
-- Name: fk_f70e6eb77d540ab; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY galleries
    ADD CONSTRAINT fk_f70e6eb77d540ab FOREIGN KEY (notice_id) REFERENCES notice__notices(id);


--
-- Name: fk_f70e6eb7a76ed395; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY galleries
    ADD CONSTRAINT fk_f70e6eb7a76ed395 FOREIGN KEY (user_id) REFERENCES user__users(id);


--
-- Name: fk_f70e6eb7e0ee4aff; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY galleries
    ADD CONSTRAINT fk_f70e6eb7e0ee4aff FOREIGN KEY (regular_id) REFERENCES user__regular(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

