const express = require('express')
const qrcode = require('qrcode-terminal')

const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys')

const app = express()

app.use(express.json())

let sock

async function startSock() {

    const { state, saveCreds } = await useMultiFileAuthState('./auth')

    const { version } = await fetchLatestBaileysVersion()

    sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: false,
        browser: ['Chrome', 'Desktop', '1.0.0']
    })

    sock.ev.on('creds.update', saveCreds)

    sock.ev.on('connection.update', async (update) => {

        const {
            connection,
            lastDisconnect,
            qr
        } = update

        if (qr) {

            console.log('\n📱 ESCANEIE O QR CODE ABAIXO:\n')

            qrcode.generate(qr, {
                small: true
            })
        }

        if (connection === 'open') {

            console.log('✅ WhatsApp conectado com sucesso!')
        }

        if (connection === 'close') {

            const statusCode =
                lastDisconnect?.error?.output?.statusCode

            console.log('⚠️ Conexão encerrada.')

            if (statusCode !== DisconnectReason.loggedOut) {

                console.log('🔄 Reconectando...')

                startSock()

            } else {

                console.log('❌ Sessão desconectada.')
            }
        }
    })
}

startSock()

app.post('/send', async (req, res) => {

    try {

        const {
            number,
            message
        } = req.body

        if (!number || !message) {

            return res.status(400).json({
                success: false,
                error: 'Número e mensagem são obrigatórios'
            })
        }

        const jid =
            number.replace(/\D/g, '') + '@s.whatsapp.net'

        await sock.sendMessage(jid, {
            text: message
        })

        console.log(`📨 Mensagem enviada para ${number}`)

        return res.status(200).json({
            success: true
        })

    } catch (error) {

        console.log('❌ Erro ao enviar:', error)

        return res.status(500).json({
            success: false,
            error: error.message
        })
    }
})

app.listen(3000, () => {

    console.log('🚀 API Baileys rodando na porta 3000')
})