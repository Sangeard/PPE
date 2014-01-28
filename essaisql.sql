delimiter //
create procedure ets()
begin 
declare 
v_essai varchar(25) ;
declare v_test varchar(2);
 insert into etat(id, libelle) values ("ok", v_essai) ;
end //
delimiter ;