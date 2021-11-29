create table parcial_usuarios(
    id int not null AUTO_INCREMENT,
    mail varchar(100) not null,
    tipo enum('cliente', 'admin') not null,
    clave varchar(100) not null,
    primary key(id),
    unique(mail));


create table parcial_criptomonedas(
    id int not null AUTO_INCREMENT,
    precio float not null,
    nombre varchar(50) not null,
    nacionalidad varchar(50) not null,
    foto varchar(100) null,
    estado enum('activo', 'inactivo') not null,
    primary key(id),
	UNIQUE (nombre));

create table parcial_ventaCriptomonedas(
    id int not null AUTO_INCREMENT,
    fecha datetime not null,
    cantidad int not null, 
    criptomoneda_id int not null,
    vendedor_id int not null,
    foto varchar(100) not null,
    primary key(id),
    foreign key (vendedor_id) references parcial_usuarios(id),
	foreign key (criptomoneda_id) references parcial_criptomonedas(id))