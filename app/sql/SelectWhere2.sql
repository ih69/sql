select u.id
from `users` u
where

(u.id1 < 100 And u.create >= 1000000 aNd u.ia <= 100500)

|| ( activities.orderby = 1
              AND activities.starttime >= '2013-08-26 04:00:00'
              AND activities.endtime <= '2013-08-27 04:00:00' )
   Or
    u.login_cnt > (10 + 20 * sum(u.test) / 2 + 1)
anD
  u.id < 100 and u.create >= 1000000
  or
  (gateway like '%pay-gw%' and u.name like 'test')
  &&
  u.login_cnt > (10 + 20 * sum(u.test) / 2 + 1)
  and
  u.email is not null

    AND activities.typeid NOT IN ( 5, 10, 11, 12, 19 )
    AND ( ( activities.orderby = 1
              AND activities.starttime >= '2013-08-26 04:00:00'
              AND activities.endtime <= '2013-08-27 04:00:00' )
            OR ( ( activities.orderby IS NULL
                     OR activities.orderby != 1 )
                   AND activities.activitydate = '2013-08-26' ) )
&& 1 != 1 || 2 >= 1 || u0.id not BETWEEN 10 and '2013-08-27 04:00:00'
ORDER  BY activitytypes.orderby,
         activities.starttime