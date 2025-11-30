
Create database roombookingsql;
use roombookingsql;
show databases;
show tables; 

Create Table Instructors(
name varchar(32),
department varchar(64),
Contact_Info VARCHAR(20),
Iid int primary key
);

Create Table Admin(
name varchar(32),
Contact_Info VARCHAR(20),
Aid int primary key
);

Create Table Rooms(
Capacity int,
Availability boolean,
Location varchar(64),
Tech_Feature text,
Rid int primary key
);

Create Table Courses(
Course_Name varchar(32),
Credit_Hours int,
 Required_Room_Features text,
 Cid int primary key
 );

Create Table Bookings(
Date_Created datetime,
Start_Time time,
End_Time time,
Status text,
Iid int,
Cid int,
Rid int,
foreign key (Iid) references Instructors (Iid),
foreign key (Cid) references Courses (Cid),
foreign key (Rid) references Rooms (Rid),
Bid int primary key
);

Create Table Room_Match(
Requirements text,
Cid int,
Rid int,
foreign key (Cid) references Courses (Cid),
foreign key (Rid) references Rooms (Rid)
);

Create Table Teaches(
Iid int,
Cid int,
foreign key (Iid) references Instructors (Iid),
foreign key (Cid) references Courses (Cid)
);

Create Table Request(
Iid int,
Bid int,
foreign key (Iid) references Instructors (Iid),
foreign key (Bid) references Bookings (Bid)
);

Create Table Approve_OR_Reject(
Aid int,
Bid int,
decision ENUM('approved','pending','rejected') NOT NULL,
decided_at DATETIME,
foreign key (Aid) references Admin (Aid),
foreign key (Bid) references Bookings (Bid)
);