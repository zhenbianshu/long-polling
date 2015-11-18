create table chat(
send char(16) not null,
receive char(16) not null,
content char(64) not null,
seread char(1) not null default 0,
curead char(1) not null default 0
)

insert into chat (send,receive,content) values("guke001","kefu","你好，少年")

update chat set seread=1 where seread=0;
update chat set seread=0 where seread=1;
