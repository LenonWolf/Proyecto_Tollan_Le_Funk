CREATE ROLE 'rol_get_sistema';
GRANT SELECT ON tollan.sistema TO 'rol_get_sistema';
GRANT SELECT ON tollan.tipo TO 'rol_get_sistema';
GRANT SELECT ON tollan.dado TO 'rol_get_sistema';
GRANT SELECT ON tollan.dado_sistema TO 'rol_get_sistema';
GRANT SELECT ON tollan.genero TO 'rol_get_sistema';
GRANT SELECT ON tollan.genero_sistema TO 'rol_get_sistema';

CREATE ROLE 'rol_lector';
GRANT SELECT ON tollan.partida TO 'rol_lector';
GRANT SELECT ON tollan.sistema TO 'rol_lector';
GRANT SELECT ON tollan.dm TO 'rol_lector';
GRANT SELECT ON tollan.tipo TO 'rol_lector';

CREATE ROLE 'rol_edit_partida';
GRANT SELECT ON tollan.partida TO 'rol_edit_partida';
GRANT SELECT ON tollan.dm TO 'rol_edit_partida';
GRANT INSERT ON tollan.partida TO 'rol_edit_partida';
GRANT INSERT ON tollan.dm TO 'rol_edit_partida';
GRANT UPDATE ON tollan.partida TO 'rol_edit_partida';

CREATE ROLE 'rol_del_partida';
GRANT DELETE ON tollan.partida TO 'rol_del_partida';

CREATE ROLE 'rol_del_usuario';
GRANT SELECT ON tollan.usuarios TO 'rol_del_usuario';
GRANT DELETE ON tollan.usuarios TO 'rol_del_usuario';

CREATE ROLE 'rol_lec_usuarios';
GRANT SELECT ON tollan.usuarios TO 'rol_lec_usuarios';

CREATE ROLE 'rol_upd_usuarios';
GRANT SELECT ON tollan.usuarios TO 'rol_upd_usuarios';
GRANT INSERT ON tollan.usuarios TO 'rol_upd_usuarios';
GRANT UPDATE ON tollan.usuarios TO 'rol_upd_usuarios';

CREATE USER 'usr_get_sistema'@'localhost' IDENTIFIED BY 'sistema123';
CREATE USER 'usr_lector'@'localhost' IDENTIFIED BY 'lector123';
CREATE USER 'usr_edit_partida'@'localhost' IDENTIFIED BY 'edit_partida123';
CREATE USER 'usr_del_partida'@'localhost' IDENTIFIED BY 'del_partida123';
CREATE USER 'usr_del_usuario'@'localhost' IDENTIFIED BY 'del_usuario123';
CREATE USER 'usr_lec_usuarios'@'localhost' IDENTIFIED BY 'lec_usuarios123';
CREATE USER 'usr_upd_usuarios'@'localhost' IDENTIFIED BY 'upd_usuarios123';

GRANT 'rol_get_sistema' TO 'usr_get_sistema'@'localhost';
SET DEFAULT ROLE 'rol_get_sistema' TO 'usr_get_sistema'@'localhost';

GRANT 'rol_lector' TO 'usr_lector'@'localhost';
SET DEFAULT ROLE 'rol_lector' TO 'usr_lector'@'localhost';

GRANT 'rol_edit_partida' TO 'usr_edit_partida'@'localhost';
SET DEFAULT ROLE 'rol_edit_partida' TO 'usr_edit_partida'@'localhost';

GRANT 'rol_del_partida' TO 'usr_del_partida'@'localhost';
SET DEFAULT ROLE 'rol_del_partida' TO 'usr_del_partida'@'localhost';

GRANT 'rol_del_usuario' TO 'usr_del_usuario'@'localhost';
SET DEFAULT ROLE 'rol_del_usuario' TO 'usr_del_usuario'@'localhost';

GRANT 'rol_lec_usuarios' TO 'usr_lec_usuarios'@'localhost';
SET DEFAULT ROLE 'rol_lec_usuarios' TO 'usr_lec_usuarios'@'localhost';

GRANT 'rol_upd_usuarios' TO 'usr_upd_usuarios'@'localhost';
SET DEFAULT ROLE 'rol_upd_usuarios' TO 'usr_upd_usuarios'@'localhost';