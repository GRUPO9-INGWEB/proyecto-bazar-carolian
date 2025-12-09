<?php
// config/correo_config.php
// AJUSTA ESTOS DATOS CON TU SMTP REAL (Gmail, Outlook, etc.)

const SMTP_HOST  = 'smtp.gmail.com';     // Servidor SMTP
const SMTP_PORT  = 587;                 // 587 (TLS) o 465 (SSL)
const SMTP_USER  = 'drudasma@ucvvirtual.edu.pe';      // tu correo
const SMTP_PASS  = 'uvghfuooimhjzxby';   // tu contraseña o "contraseña de aplicación"
const SMTP_SECURE = 'tls';               // 'tls' o 'ssl'

const SMTP_FROM      = SMTP_USER;        // desde qué correo se envía
const SMTP_FROM_NAME = 'Bazar Carolian'; // nombre que verá el cliente
