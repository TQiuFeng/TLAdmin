<?php

namespace app\adminapi\controller;

use app\adminapi\vo\ApiDocsAuthVo;
use app\adminapi\vo\ApiResponseFormatVo;
use app\adminapi\vo\SecurityRateLimitVo;
use app\adminapi\vo\SecurityTotpVo;
use app\common\exception\BizException;
use app\common\router\GetMapping;
use app\common\router\PostMapping;
use app\common\router\RestController;

/**
 * 系统配置接口。
 *
 * 当前先提供 API 默认返回格式 JSON/XML 的读取和保存能力。
 * Author: qiufeng
 */
#[RestController('/adminapi/system/config', tag: '系统配置')]
final class SystemConfigController extends BaseController
{
    #[GetMapping('/api-response-format', permission: 'system:config:list', summary: '查看 API 默认响应格式')]
    public function apiResponseFormat(): ApiResponseFormatVo
    {
        return ApiResponseFormatVo::from([
            'field' => 'api.response_format',
            'value' => $this->config->get('api.response_format', 'json'),
            'options' => $this->config->get('api.response_format_options', ['json', 'xml']),
        ]);
    }

    #[PostMapping('/api-response-format', permission: 'system:config:update', summary: '修改 API 默认响应格式', message: '保存成功')]
    public function updateApiResponseFormat(): ApiResponseFormatVo
    {
        // 兼容 value(与 GET 返回字段一致)和 format 两种参数名
        $format = strtolower(trim((string) $this->input('value', $this->input('format', ''))));
        $options = $this->config->get('api.response_format_options', ['json', 'xml']);

        if (!in_array($format, $options, true)) {
            throw BizException::paramError('API 返回格式只能选择 json 或 xml');
        }

        $this->config->set('api.response_format', $format);
        $this->config->save();

        return ApiResponseFormatVo::from([
            'field' => 'api.response_format',
            'value' => $format,
            'options' => $options,
        ]);
    }

    /** 查看全局时间显示格式(前端按此格式渲染所有时间戳) */
    #[GetMapping('/datetime-format', permission: 'system:config:list', summary: '查看全局时间显示格式')]
    public function datetimeFormat(): array
    {
        return [
            'value' => (string) $this->config->get('api.datetime_format', 'YYYY-MM-DD HH:mm:ss'),
            'presets' => [
                'YYYY-MM-DD HH:mm:ss',
                'YYYY-MM-DD HH:mm',
                'YYYY-MM-DD',
                'YYYY/MM/DD HH:mm:ss',
                'MM-DD HH:mm',
                'YYYY年MM月DD日 HH:mm:ss',
                'YYYY年MM月DD日',
            ],
        ];
    }

    /** 修改全局时间显示格式 {value},dayjs 格式串;不限制具体值,前端按 dayjs 解析 */
    #[PostMapping('/datetime-format', permission: 'system:config:update', summary: '修改全局时间显示格式', message: '保存成功')]
    public function updateDatetimeFormat(): array
    {
        $value = trim((string) $this->input('value', ''));
        if ($value === '') {
            throw BizException::paramError('时间格式不能为空');
        }

        $this->config->set('api.datetime_format', $value);
        $this->config->save();

        return ['value' => $value];
    }

    /** 查看动态验证码(TOTP)全局开关 */
    #[GetMapping('/security-totp', permission: 'system:config:list', summary: '查看动态验证码全局开关')]
    public function securityTotp(): SecurityTotpVo
    {
        return SecurityTotpVo::from([
            'enabled' => (bool) $this->config->get('security.totp.enabled', false),
            'issuer' => (string) $this->config->get('security.totp.issuer', 'TLAdmin'),
        ]);
    }

    /** 修改动态验证码(TOTP)全局开关 */
    #[PostMapping('/security-totp', permission: 'system:config:update', summary: '修改动态验证码全局开关 {enabled, issuer}', message: '保存成功')]
    public function updateSecurityTotp(): SecurityTotpVo
    {
        $enabled = filter_var($this->input('enabled', false), FILTER_VALIDATE_BOOL);

        $this->config->set('security.totp.enabled', $enabled);

        $issuer = trim((string) $this->input('issuer', ''));
        if ($issuer !== '') {
            $this->config->set('security.totp.issuer', $issuer);
        }

        $this->config->save();

        return SecurityTotpVo::from([
            'enabled' => $enabled,
            'issuer' => (string) $this->config->get('security.totp.issuer', 'TLAdmin'),
        ]);
    }

    /** 查看接口限流阈值 */
    #[GetMapping('/security-rate-limit', permission: 'system:config:list', summary: '查看接口限流阈值')]
    public function securityRateLimit(): SecurityRateLimitVo
    {
        return SecurityRateLimitVo::from([
            'per_second' => (int) $this->config->get('security.rate_limit.per_second', 30),
        ]);
    }

    /** 修改接口限流阈值 */
    #[PostMapping('/security-rate-limit', permission: 'system:config:update', summary: '修改接口限流阈值 {per_second},0 表示不限流', message: '保存成功')]
    public function updateSecurityRateLimit(): SecurityRateLimitVo
    {
        $perSecond = (int) $this->input('per_second', -1);
        if ($perSecond < 0 || $perSecond > 10000) {
            throw BizException::paramError('每秒请求次数须在 0(不限流)到 10000 之间');
        }

        $this->config->set('security.rate_limit.per_second', $perSecond);
        $this->config->save();

        return SecurityRateLimitVo::from(['per_second' => $perSecond]);
    }

    /** 查看接口文档(OpenAPI 调试台)访问账号配置;不回显密码 */
    #[GetMapping('/api-docs-auth', permission: 'system:config:list', summary: '查看接口文档访问账号配置')]
    public function apiDocsAuth(): ApiDocsAuthVo
    {
        return ApiDocsAuthVo::from([
            'enabled' => (bool) $this->config->get('api.docs.auth.enabled', false),
            'username' => (string) $this->config->get('api.docs.auth.username', ''),
            'has_password' => (string) $this->config->get('api.docs.auth.password', '') !== '',
        ]);
    }

    /**
     * 修改接口文档访问账号配置 {enabled, username, password}。
     * password 留空表示沿用已有密码;开启保护时账号和密码均不能为空。
     */
    #[PostMapping('/api-docs-auth', permission: 'system:config:update', summary: '修改接口文档访问账号配置 {enabled, username, password}', message: '保存成功')]
    public function updateApiDocsAuth(): ApiDocsAuthVo
    {
        $enabled = filter_var($this->input('enabled', false), FILTER_VALIDATE_BOOL);
        $username = trim((string) $this->input('username', ''));
        // 密码不 trim,允许首尾空格
        $password = (string) $this->input('password', '');
        $existingPassword = (string) $this->config->get('api.docs.auth.password', '');

        if ($enabled) {
            if ($username === '') {
                throw BizException::paramError('开启文档访问保护时账号不能为空');
            }
            if ($password === '' && $existingPassword === '') {
                throw BizException::paramError('开启文档访问保护时必须设置密码');
            }
        }

        $this->config->set('api.docs.auth.enabled', $enabled);
        if ($username !== '') {
            $this->config->set('api.docs.auth.username', $username);
        }
        if ($password !== '') {
            $this->config->set('api.docs.auth.password', $password);
        }
        $this->config->save();

        return ApiDocsAuthVo::from([
            'enabled' => $enabled,
            'username' => (string) $this->config->get('api.docs.auth.username', ''),
            'has_password' => (string) $this->config->get('api.docs.auth.password', '') !== '',
        ]);
    }
}
