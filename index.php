import json,config
from flask import Flask, request, jsonify
from binance.client import Client
from binance.enums import *

app = Flask(__name__)

client = Client(config.API_KEY, config.API_SECRET)
studentsList = []
def order(side, symbol, price, quantity, order_type=ORDER_TYPE_LIMIT):
    try:
        print(f"sending order {order_type} - {side} {quantity} {symbol}")
        order = client.futures_create_order(symbol=symbol, side=side,price=price, type=order_type, quantity=quantity, timeInForce='GTC',)
        orderId = order["orderId"]
        print('Sell order nr '+str(orderId)+' placed at {}\n'.format(price))


        print(order)
    except Exception as e:
        print("an exception occured - {}".format(e))
        return False

    return order
#returneaza

@app.route("/webhook",methods=['POST'])
def webhook():
    print(request.data)
    data = json.loads(request.data)

    for key,value in data.items():
        print(key, ":", value)
        print(value['PAIR'])
        order_response = order("BUY",f"{value['PAIR']}",f"{value['PRICE']}", 0.2)
        if order_response:
            return{
                "code":"successes",
                "message":"oreder executed"
            }
        else:
            print("order failed")

            return{
                "code":"error",
                "message":"order failed"
            }
        
        

    orders = client.futures_get_order(symbol = 'SOLUSDT', orderId= 20294412852)
    print('Aceasta are statusul : '+orders['status'])
        
    #if config.WEBHOOK_PASSPHRASE == "ALABALA":
    #    print("aaaa")
    
    

    return {
        "code":"success",
        #"message":data
    }
