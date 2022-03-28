# Manage your transactions
A application that you can pay and get paid by anyone

## Endpoints
### [POST]api/transaction
Create a transaction, by giving the id of two valid users and the amount to be transffered
**Fields**:
- amount
- payee_id
- payer_id

### [GET]api/transaction/statement/{wallet}
Get the statement of a specific wallet in a range of dates, passed by a query param
**Fields**:
- period_start
- period_end

Both dates according strtotime
