import requests
import os
import time
# pyrefly: ignore [missing-import]
import mysql.connector
# pyrefly: ignore [missing-import]
from dotenv import load_dotenv

load_dotenv()

def enviar_whatsapp(appt):
    url = "http://127.0.0.1:8080/message/sendText/barber_notifier"
    
    # Tratamento de Data e Hora para o formato brasileiro
    data_formatada = appt['appointment_date'].strftime('%d/%m/%Y')
    horario_formatado = str(appt['appointment_time'])[:5]

    msg = f"""✂️ *BARBEARIA* — Confirmação de Agendamento

Olá, *{appt['user_name']}*! 👋

Seu agendamento foi confirmado com sucesso:

📅 *Data:* {data_formatada}
⏰ *Horário:* {horario_formatado}
💈 *Serviço:* {appt['service_name']}
💰 *Valor:* R$ {appt['price']}

Caso precise cancelar ou reagendar, entre em contato conosco.
Te esperamos! 🙌"""

    payload = {
        "number": '55' + appt['phone'],
        "textMessage": {"text": msg},
        "delay": 123,
        "linkPreview": True
    }
    
    headers = {
        "apikey": os.getenv("AUTHENTICATION_API_KEY"),
        "Content-Type": "application/json"
    }

    try:
        response = requests.post(url, json=payload, headers=headers, timeout=10)
        return response.status_code in [200, 201]
    except Exception as e:
        print(f"⚠️ Erro ao chamar Evolution API: {e}")
        return False

def buscar_e_notificar():
    db = None
    try:
        db = mysql.connector.connect(
            host=os.getenv("DB_HOST"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASS"),
            database=os.getenv("DB_NAME")
        )
        cursor = db.cursor(dictionary=True)

        # Query utilizando os nomes exatos do seu banco de dados
        query = """
            SELECT 
                a.id, 
                a.appointment_date, 
                a.appointment_time, 
                u.name as user_name, 
                u.phone, 
                s.name as service_name, 
                s.price 
            FROM appointments a
            JOIN users u ON a.user_id = u.id
            JOIN services s ON a.service_id = s.id
            WHERE a.notification_status = 'pendente'
            LIMIT 5
        """
        
        cursor.execute(query)
        agendamentos = cursor.fetchall()

        for appt in agendamentos:
            print(f"🔔 Processando ID {appt['id']} ({appt['user_name']})...")
            
            if enviar_whatsapp(appt):
                update_query = "UPDATE appointments SET notification_status = 'enviado' WHERE id = %s"
                cursor.execute(update_query, (appt['id'],))
                db.commit()
                print(f"✅ Enviado com sucesso para {appt['phone']}")
            else:
                print(f"❌ Falha no envio para {appt['user_name']}")

    except mysql.connector.Error as err:
        print(f"❌ Erro no MySQL: {err}")
    finally:
        if db and db.is_connected():
            cursor.close()
            db.close()

if __name__ == "__main__":
    print("🚀 Bot de Notificações Ativo!")
    print(f"Monitorando banco: {os.getenv('DB_NAME')}")
    
    while True:
        try:
            buscar_e_notificar()
        except Exception as e:
            print(f"💥 Erro crítico no loop: {e}")
        
        time.sleep(10) # Verifica a cada 10 segundos