<?php

require_once( 'db.php' );

function get_all_schools() {
	global $conn;

	$query = "SELECT u.univ_name FROM Universities u";
	$res = pg_query( $conn, $query );

	if( !$res ) 
		die( 'bad universities database : ' . pg_last_error() );

	$result = array();
	while( $row = pg_fetch_row( $res ) )
		$result[] = array( $row[0] );

	return $result;
}

function restriction_to_sql( $r ) {
	switch( $r['restriction'] ) {
		case 'Course Number':
			switch( $r['operator'] ) {
				case 'Equals':
					return "c.course_num = '{$r['keyword']}'";
				case 'Prefix':
					return "c.course_num LIKE '{$r['keyword']}%'";
				case 'Contains':
					return "c.course_num LIKE '%{$r['keyword']}%'";
				case 'One of':
					$k = "'" . join( "','", explode( ',', $r['keyword'] ) ) . "'";
					return "c.course_num IN($k)";
				case 'Less Than':
					return "CAST( c.course_num AS INT ) <= {$r['keyword']}";
				case 'Greater Than':
					return "CAST( c.course_num AS INT ) >= {$r['keyword']}";
				default:
					die( 'bad operator:' . $r['operator'] );
			}
		break;

		case 'Professor Name':
			switch( $r['operator'] ) {
				case 'Equals':
					return "p.prof_name = '{$r['keyword']}'";
				case 'Prefix':
					return "p.prof_name LIKE '{$r['keyword']}%'";
				case 'Contains':
					return "p.prof_name LIKE '%{$r['keyword']}%'";
				case 'One of':
					$k = "'" . join( "','", explode( ',', $r['keyword'] ) ) . "'";
					return "p.prof_name IN($k)";

				default:
					die( 'bad operator:' . $r['operator'] );
			}
		case 'Department Name':
			switch( $r['operator'] ) {
				case 'Equals':
					return "d.depart_name = '{$r['keyword']}'";
				case 'Prefix':
					return "d.depart_name LIKE '{$r['keyword']}%'";
				case 'Contains':
					return "d.depart_name LIKE '%{$r['keyword']}%'";
				case 'One of':
					$k = "'" . join( "','", explode( ',', $r['keyword'] ) ) . "'";
					return "d.depart_name IN($k)";

				default:
					die( 'bad operator: ' . $r['operator'] );
			}
	}

	var_dump( $r );
	die( 'bad restriction' );
}

function get_selectors( $criteria ) {
	$fields = array();

	foreach( $criteria as $c ) {
		switch( $c ) {
			case 'Simplicity':
				$fields[] = 'TRUNC(AVG(co.easiness),3)';
			break;
			case 'Availability':
				$fields[] = 'TRUNC(AVG(co.helpfulness),3)';
			break;
			case 'Interest':
				$fields[] = 'TRUNC(AVG(co.interest),3)';
			break;
			case 'Quality':
				$fields[] = 'TRUNC(AVG(co.quality),3)';
			break;
			case 'Clarity':
				$fields[] = 'TRUNC(AVG(co.clarity),3)';
			break;
			default:
				continue;
		}
	}

	return join( ' , ', $fields );
}

function get_entity( $entity ) {
	switch( $entity ) {
		case 'Professor':
			return 'p.prof_name';
		case 'Course':
			return 'c.course_num';
		case 'Department':
			return 'd.depart_name';
		default:
			die( 'bad entity' );
	}
}

function process_query( $entity, $criteria, $restrictions ) {
	global $conn;

	$selectors = get_selectors( $criteria );
	$entity_name = get_entity( $entity );

	$query = "SELECT {$entity_name},{$selectors} FROM Universities u
		  INNER JOIN Departments d ON u.univ_id = d.univ_id
	          INNER JOIN Courses c ON d.depart_id = c.depart_id
		  INNER JOIN ProfessorDepartmentXRef pdx ON d.depart_id = pdx.depart_id
		  INNER JOIN ProfessorCourseXRef pcx ON c.course_id = pcx.course_id
		  INNER JOIN Professors p ON p.prof_id = pdx.prof_id 
		  INNER JOIN Comments co ON co.course_id = c.course_id AND co.prof_id = p.prof_id WHERE ";

	$clauses = array();
	foreach( $restrictions as $r )
		$clauses[] =  restriction_to_sql( $r );
	
	$flat_clauses = join( ' AND ', $clauses );
	$query .= $flat_clauses;
	$query .= " GROUP BY {$entity_name} ";
	$query .= " ORDER BY {$entity_name} ASC ";
	
	$res = pg_query( $conn, $query );
	if( !$res ) die( 'query error: ' . $query );
	
	$result = array();
	while( $row = pg_fetch_row( $res ) )
		$result[] = $row;
	return $result;

}
