delete from auth_access;
delete from auth_user_group;
delete from auth_transaction;
delete from auth_group;
delete from auth_log;
delete from auth_user;
delete from auth_person;

insert into auth_person (name,nick,email) values ('Ely Matos','Ely','ely.matos@gmail.com');
insert into auth_user(login,passMD5,theme,active,idPerson)
select 'ematos',md5('test'),'default',1,idPerson
from auth_person where nick='Ely';

insert into auth_group (name) values ('ADMIN');

insert into auth_user_group (idUser,idGroup) 
select idUser, idGroup
from auth_user,auth_group
where auth_user.login='ematos'
and auth_group.name = 'ADMIN';


