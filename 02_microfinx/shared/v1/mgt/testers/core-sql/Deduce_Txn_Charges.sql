USE wvi;

-- TRANSACTION CHARGS
SELECT * FROM txn_types;
SELECT * FROM txn_charge_events;
SELECT * FROM txn_charge_event_items;
SELECT * FROM txn_charges;
SELECT * FROM txn_charge_amounts;

-- LEVEL 02
SELECT * FROM txn_charge_events
WHERE TRAN_CHRG_STATUS='ACTIVE'
  AND CHRG_EVNT_ID=(select CHRG_EVENT_ID from txn_types where TRAN_TYPE_ID='TTC0003');


-- LEVEL 03
SELECT * FROM txn_charge_event_items
WHERE CHRG_EVENT_ITEM_STATUS='ACTIVE' 
  AND TRAN_CHRG_ID!='HJK0001'
  AND CHRG_EVNT_ID=(
	  SELECT CHRG_EVNT_ID FROM txn_charge_events
	  WHERE TRAN_CHRG_STATUS='ACTIVE'
	    AND CHRG_EVNT_ID=(select CHRG_EVENT_ID from txn_types where TRAN_TYPE_ID='TTC0003')
      );
      
      
 -- LEVEL 04
SELECT * FROM txn_charges
WHERE TRAN_CHRG_STATUS='ACTIVE' 
  AND TRAN_CHRG_ID!='HJK0001'
  AND TRAN_CHRG_ID in ( 
     SELECT TRAN_CHRG_ID FROM txn_charge_event_items
	 WHERE CHRG_EVENT_ITEM_STATUS='ACTIVE' 
       AND CHRG_EVNT_ID=(
			SELECT CHRG_EVNT_ID FROM txn_charge_events
			WHERE TRAN_CHRG_STATUS='ACTIVE'
	          AND CHRG_EVNT_ID=(select CHRG_EVENT_ID from txn_types where TRAN_TYPE_ID='TTC0005')
      )
);
      
      
      
      
