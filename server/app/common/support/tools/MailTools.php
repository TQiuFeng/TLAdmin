<?php

namespace app\common\support\tools;

/**
 * 邮件发送工具。
 *
 * 基于 PHPMailer 走 SMTP 发送，支持 SSL/TLS、HTML 正文、附件、抄送密送。
 * SMTP 配置优先取 $options，缺省回退 .env（MAIL_HOST/MAIL_PORT/MAIL_USERNAME/MAIL_PASSWORD/MAIL_ENCRYPTION/MAIL_FROM/MAIL_FROM_NAME）。
 * Author: qiufeng
 */
trait MailTools
{
    /**
     * 发送邮件,失败不抛异常而是返回带 error 的结果。
     *
     * @param string|array $to      收件人:单个地址、地址数组,或 ['邮箱' => '昵称']
     * @param string       $subject 邮件主题
     * @param string       $content 正文(默认按 HTML 处理)
     * @param array        $options 可选项:host、port、username、password、encryption(ssl|tls|none)、
     *                              from、from_name、html、attachments、cc、bcc、reply_to、timeout;
     *                              缺省回退 .env 的 MAIL_* 配置
     * @return array{ok:bool,error:string} 发送结果
     */
    public static function sendMail(string|array $to, string $subject, string $content, array $options = []): array
    {
        if (!class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            throw new \RuntimeException('缺少 Composer 包：phpmailer/phpmailer');
        }

        $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $host = $options['host'] ?? self::mailEnv('MAIL_HOST');
            $username = $options['username'] ?? self::mailEnv('MAIL_USERNAME');
            $password = $options['password'] ?? self::mailEnv('MAIL_PASSWORD');
            if ($host === '' || $username === '') {
                throw new \InvalidArgumentException('SMTP 配置缺失：请传入 host/username/password 或配置 .env 的 MAIL_* 项');
            }

            $encryption = strtolower((string) ($options['encryption'] ?? (self::mailEnv('MAIL_ENCRYPTION') ?: 'ssl')));

            $mailer->isSMTP();
            $mailer->CharSet = 'UTF-8';
            $mailer->Host = $host;
            $mailer->SMTPAuth = true;
            $mailer->Username = $username;
            $mailer->Password = $password;
            $mailer->Port = (int) ($options['port'] ?? (self::mailEnv('MAIL_PORT') ?: ($encryption === 'ssl' ? 465 : 587)));
            $mailer->Timeout = (int) ($options['timeout'] ?? 10);
            if ($encryption === 'ssl') {
                $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls') {
                $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mailer->SMTPAutoTLS = false;
            }

            $from = $options['from'] ?? (self::mailEnv('MAIL_FROM') ?: $username);
            $mailer->setFrom($from, $options['from_name'] ?? (self::mailEnv('MAIL_FROM_NAME') ?: ''));

            foreach (self::mailAddressList($to) as [$address, $name]) {
                $mailer->addAddress($address, $name);
            }
            foreach (self::mailAddressList($options['cc'] ?? []) as [$address, $name]) {
                $mailer->addCC($address, $name);
            }
            foreach (self::mailAddressList($options['bcc'] ?? []) as [$address, $name]) {
                $mailer->addBCC($address, $name);
            }
            if (!empty($options['reply_to'])) {
                $mailer->addReplyTo($options['reply_to']);
            }

            foreach ((array) ($options['attachments'] ?? []) as $attachment) {
                $mailer->addAttachment($attachment);
            }

            $mailer->isHTML($options['html'] ?? true);
            $mailer->Subject = $subject;
            $mailer->Body = $content;
            if (($options['html'] ?? true) === true) {
                $mailer->AltBody = trim(strip_tags($content));
            }

            $mailer->send();

            return ['ok' => true, 'error' => ''];
        } catch (\Throwable $throwable) {
            return ['ok' => false, 'error' => $mailer->ErrorInfo ?: $throwable->getMessage()];
        }
    }

    private static function mailEnv(string $key): string
    {
        if (function_exists('env')) {
            try {
                $value = env($key);
                if ($value !== null && $value !== false && $value !== '') {
                    return (string) $value;
                }
            } catch (\Throwable) {
                // 非框架环境下 env() 助手可能不可用，回退 getenv
            }
        }

        $value = getenv($key);

        return $value === false ? '' : (string) $value;
    }

    private static function mailAddressList(string|array $input): array
    {
        if (is_string($input)) {
            return $input === '' ? [] : [[$input, '']];
        }

        $list = [];
        foreach ($input as $key => $value) {
            if (is_string($key)) {
                $list[] = [$key, (string) $value];
            } else {
                $list[] = [(string) $value, ''];
            }
        }

        return $list;
    }
}
