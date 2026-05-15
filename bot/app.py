import requests
import os
import mysql.connector
from dotenv import load_dotenv
import traceback

load_dotenv()

BAILEYS_API_URL = "http://127.0.0.1:3000/send"


def enviar_whatsapp(appt):

    dia_semana = appt['appointment_date'].weekday()

    match dia_semana:
        case 1:
            dia_semana = ', Terça-feira'
        case 2:
            dia_semana = ', Quarta-feira'
        case 3:
            dia_semana = ', Quinta-feira'
        case 4:
            dia_semana = ', Sexta-feira'
        case 5:
            dia_semana = ', Sábado'
        case _:
            dia_semana = ''

    data_formatada = appt['appointment_date'].strftime('%d/%m/%Y')
    horario_formatado = str(appt['appointment_time'])[:5]

    msg = f"""✂️ *BARBEARIA* — Confirmação de Agendamento

Olá, *{appt['user_name']}*! 👋

Seu agendamento foi confirmado com sucesso:

📅 *Data:* {data_formatada}{dia_semana}
⏰ *Horário:* {horario_formatado}
💈 *Serviço:* {appt['service_name']}
💰 *Valor:* R$ {appt['price']}

Caso precise cancelar ou reagendar, entre em contato conosco.
Te esperamos! 🙌"""
    
    payload = {
        "number": '55' + appt['phone'],
        "message": msg
    }

    try:

        response = requests.post(
            BAILEYS_API_URL,
            json=payload,
            timeout=15
        )

        print(f"📨 Status API: {response.status_code}")
        print(f"📨 Resposta: {response.text}")

        return response.status_code in [200, 201]
    
    except Exception as e:
        print(f"⚠️ Erro ao chamar API Baileys: {e}")
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

        if not agendamentos:
            print("📭 Nenhum agendamento pendente.")

        for appt in agendamentos:

            print(f"🔔 Processando ID {appt['id']} ({appt['user_name']})...")

            if enviar_whatsapp(appt):

                update_query = """
                    UPDATE appointments
                    SET notification_status = 'enviado'
                    WHERE id = %s
                """

                cursor.execute(update_query, (appt['id'],))
                db.commit()

                print(f"✅ Enviado com sucesso para {appt['phone']}")

            else:

                print(f"❌ Falha no envio para {appt['user_name']}")

    except mysql.connector.Error as err:

        print(f"❌ Erro no MySQL: {err}")

    except Exception as e:

        print(f"💥 Erro geral: {e}")
        traceback.print_exc()

    finally:

        if db and db.is_connected():
            cursor.close()
            db.close()
        

if __name__ == "__main__":

    print("🚀 Bot de Notificações Baileys Ativo!")
    print(f"📦 Banco monitorado: {os.getenv('DB_NAME')}")

    buscar_e_notificar()