#
# SQL statements to help save typing when querying and making changes
#

#
#  Trying to figure out how to add a user....
#
desc jos_users;
select id, name, sendemail, gid, registerDate, lastvisitDate, activation, params from jos_users where id > 77 order by id;
select id, name, username, email, password, usertype, block, sendemail from jos_users where id > 77 order by id;
select * from jos_core_acl_aro where value > 77;

update jos_users set gid  = 19 where id = 81;
update jos_users set usertype = 'Author' where id = 81;

#
#  Working with jos_joomoouser:
#
insert into jos_joomoouser set user_id = 68, comment_posted_email = 'N';
insert into jos_joomoouser set user_id = 68, comment_posted_email = 'E';
select * from jos_joomoouser;

#
# joining jos_users and jos_joomoouser:
#
select jmu.id, jmu.user_id, u.name, u.username, u.email, jmu.comment_posted_email from jos_joomoouser as jmu inner join jos_users as u on jmu.user_id = u.id;

