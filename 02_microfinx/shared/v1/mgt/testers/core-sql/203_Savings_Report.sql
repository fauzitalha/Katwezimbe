-- 01: BAL AS ON A GIVEN DATE
-----------------------------------------------------------------------------------------------------------------
SELECT (select c.display_name from m_client c where c.id=s.client_id) CLIENT_NAME
  ,(select c.external_id from m_client c where c.id=s.client_id) OLD_KHASAKH_ID
  ,(select c.account_no from m_client c where c.id=s.client_id) NEW_KHASAKH_ID
  ,s.account_no SAVINGS_ACCT_NO
  ,(select p.name from m_savings_product p where p.id=s.product_id) SAVINGS_PRODUCT
  ,s.activatedon_date ACCT_CREATION_DATE 
  ,(SELECT a.running_balance_derived 
    FROM m_savings_account_transaction a 
	WHERE a.id=(SELECT max(b.id) 
	          FROM m_savings_account_transaction b
			  WHERE b.savings_account_id=s.id 
			    and b.transaction_date=(SELECT max(c.transaction_date) 
				                        FROM m_savings_account_transaction c
									    WHERE c.running_balance_derived is not NULL 
									      and c.savings_account_id=s.id 
										  and c.transaction_date<='${asOn}'))
    ) ACCT_BAL
FROM m_savings_account s
WHERE s.product_id='${savingsProductId}'
AND s.status_enum='300'
-----------------------------------------------------------------------------------------------------------------



SELECT savings_account_id,running_balance_derived,transaction_date FROM m_savings_account_transaction;
mysql> desc m_savings_account_transaction;
+--------------------------------+---------------+------+-----+---------+----------------+
| Field                          | Type          | Null | Key | Default | Extra          |
+--------------------------------+---------------+------+-----+---------+----------------+
| id                             | bigint(20)    | NO   | PRI | NULL    | auto_increment |
| savings_account_id             | bigint(20)    | NO   | MUL | NULL    |                |
| office_id                      | bigint(20)    | NO   | MUL | NULL    |                |
| payment_detail_id              | bigint(20)    | YES  | MUL | NULL    |                |
| transaction_type_enum          | smallint(5)   | NO   |     | NULL    |                |
| is_reversed                    | tinyint(1)    | NO   |     | NULL    |                |
| transaction_date               | date          | NO   |     | NULL    |                |
| amount                         | decimal(19,6) | NO   |     | NULL    |                |
| overdraft_amount_derived       | decimal(19,6) | YES  |     | NULL    |                |
| balance_end_date_derived       | date          | YES  |     | NULL    |                |
| balance_number_of_days_derived | int(11)       | YES  |     | NULL    |                |
| running_balance_derived        | decimal(19,6) | YES  |     | NULL    |                |
| cumulative_balance_derived     | decimal(19,6) | YES  |     | NULL    |                |
| created_date                   | datetime      | NO   |     | NULL    |                |
| appuser_id                     | bigint(20)    | YES  |     | NULL    |                |
+--------------------------------+---------------+------+-----+---------+----------------+
15 rows in set (0.00 sec)


mysql> SELECT id, savings_account_id,running_balance_derived,transaction_date FROM m_savings_account_transaction WHERE savings_account_id='20' ORDER BY transaction_date, id ASC;
+-----+--------------------+-------------------------+------------------+
| id  | savings_account_id | running_balance_derived | transaction_date |
+-----+--------------------+-------------------------+------------------+
| 151 |                 20 |         10000000.000000 | 2019-06-30       |
| 179 |                 20 |          4000000.000000 | 2019-06-30       |
| 177 |                 20 |         10000000.000000 | 2019-06-30       |
| 171 |                 20 |          4000000.000000 | 2019-06-30       |
| 172 |                 20 |                    NULL | 2019-07-01       |
| 187 |                 20 |          4000109.590000 | 2019-07-01       |
| 184 |                 20 |         10000109.590000 | 2019-07-01       |
| 180 |                 20 |          4000109.590000 | 2019-07-01       |
| 169 |                 20 |                    NULL | 2019-07-01       |
| 178 |                 20 |                    NULL | 2019-07-01       |
| 202 |                 20 |         10000109.590000 | 2019-07-02       |
| 254 |                 20 |         10008441.810000 | 2019-08-01       |
| 277 |                 20 |         10016945.620000 | 2019-09-01       |
+-----+--------------------+-------------------------+------------------+

SELECT xx.TXN_ID
	  ,xx.ACCT_ID
	  ,xx.RUN_BAL
	  ,xx.TRAN_DATE 
FROM 
(
	SELECT id as TXN_ID
		  ,savings_account_id as ACCT_ID
		  ,running_balance_derived as RUN_BAL
		  ,transaction_date as TRAN_DATE
	FROM m_savings_account_transaction 
	WHERE savings_account_id='20' 
	  and transaction_date<='2019-06-30'
	ORDER BY transaction_date ASC
	UNION
	SELECT '' as TXN_ID
		  ,'' as ACCT_ID
		  ,(SELECT SUM(running_balance_derived) FROM m_savings_account_transaction WHERE savings_account_id='20' and transaction_date<='2019-06-30') as RUN_BAL
		  ,'' as TRAN_DATE
	FROM dual
) xx;

SELECT xx.TXN_ID
,xx.ACCT_ID
,xx.RUN_BAL
,xx.TRAN_DATE 
FROM 
(SELECT id as TXN_ID
	  ,savings_account_id as ACCT_ID
	  ,running_balance_derived as RUN_BAL
	  ,transaction_date as TRAN_DATE
FROM m_savings_account_transaction 
WHERE savings_account_id='20'
ORDER BY transaction_date ASC
UNION ALL 
SELECT '' as TXN_ID
      ,'Total' as ACCT_ID
	  ,(SELECT SUM(running_balance_derived) FROM m_savings_account_transaction WHERE savings_account_id='20') as RUN_BAL
	  ,'----------' as TRAN_DATE
FROM dual) xx;
ORDER BY xx.TRAN_DATE ASC
	


SELECT * FROM (SELECT id as TXN_ID
,savings_account_id as ACCT_ID
,running_balance_derived as RUN_BAL
,transaction_date as TRAN_DATE
FROM m_savings_account_transaction 
WHERE savings_account_id='20'
ORDER BY transaction_date ASC) as AA
UNION ALL
SELECT * FROM (SELECT '----------------' as TXN_ID
,'----------------' as ACCT_ID
,'----------------' as RUN_BAL
,'----------------' as TRAN_DATE
FROM dual) as BB
UNION ALL
SELECT * FROM (SELECT '' as TXN_ID
,'' as ACCT_ID
,(SELECT SUM(running_balance_derived) FROM m_savings_account_transaction WHERE savings_account_id='20') as RUN_BAL
,'' as TRAN_DATE
FROM dual) as CC;










	



SELECT running_balance_derived FROM m_savings_account_transaction WHERE id=(SELECT max(id) FROM m_savings_account_transaction WHERE savings_account_id='20' and transaction_date=(SELECT max(transaction_date) FROM m_savings_account_transaction WHERE running_balance_derived is not NULL and savings_account_id='20' and transaction_date<='2019-06-30'));
SELECT max(id) FROM m_savings_account_transaction WHERE savings_account_id='20' and transaction_date=(SELECT max(transaction_date) FROM m_savings_account_transaction WHERE running_balance_derived is not NULL and savings_account_id='20' and transaction_date<='2019-06-30');


SELECT max(transaction_date) FROM m_savings_account_transaction WHERE running_balance_derived is not NULL and savings_account_id='20' and transaction_date<='2019-09-01';

mysql> SELECT max(transaction_date) FROM m_savings_account_transaction WHERE running_balance_derived is not NULL and savings_account_id='20' and transaction_date<='2019-09-01';
mysql> SELECT running_balance_derived FROM m_savings_account_transaction WHERE savings_account_id='20' and transaction_date<='2019-09-01' ORDER BY transaction_date DESC LIMIT 1;
+-------------------------+
| running_balance_derived |
+-------------------------+
|         10000000.000000 |
+-------------------------+
1 row in set (0.01 sec)


mysql> SELECT running_balance_derived FROM m_savings_account_transaction 
WHERE savings_account_id='20' and transaction_date<='2019-08-01' ORDER BY transaction_date, id DESC LIMIT 1;
+-------------------------+
| running_balance_derived |
+-------------------------+
|          4000000.000000 |
+-------------------------+
1 row in set (0.00 sec)


