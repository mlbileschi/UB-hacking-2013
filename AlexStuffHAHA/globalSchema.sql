create table Universities (
	univ_id serial primary key,
	univ_name varchar(50)
);

create table Departments (
	depart_id serial primary key,
	depart_name varchar(50),
	univ_id int4 references Universities(univ_id)
);

create table Professors (
	prof_id serial primary key,
	prof_name varchar(50),
	prof_qual numeric(2,1),
	jsp_is varchar(50)
);

create table Courses (
	course_id serial primary key,
	course_num varchar(50),
	depart_id int4 references Departments(depart_id)
);

create table Comments (
	comment_id serial primary key,
	easiness numeric(2,1),
	helpfulness numeric(2,1),
	interest numeric(2,1),
	quality int4,
	clarity numeric(2,1),
	comment_text varchar(1000),
	time_stamp timestamp,
	course_id int4 references Courses(course_id),
	prof_id int4 references Professors(prof_id)	
);

create table ProfessorCourseXRef (
	prof_id int4 references Professors(prof_id),
	course_id int4 references Courses(course_id)
);

create table ProfessorDepartmentXRef (
	prof_id int4 references Professors(prof_id),
	depart_id int4 references Departments(depart_id)
);