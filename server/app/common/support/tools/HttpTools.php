<?php

namespace app\common\support\tools;

/**
 * 统一 HTTP 客户端工具。
 *
 * 支持 GET/POST/PUT/PATCH/DELETE/HEAD/OPTIONS，以及 JSON、form、multipart、raw body；
 * 并支持流式响应（SSE/chunked），可直接对接 DeepSeek、OpenAI 等 AI 模型接口。
 * 优先使用 Guzzle；没有安装依赖时用 PHP stream 做最小兜底。
 * Author: qiufeng
 */
trait HttpTools
{
    /**
     * 出站请求结束后的回调 fn(array $result): void。
     * TLAdmin 在入口处挂上第三方请求日志落库;为 null 时不做任何事。
     */
    public static ?\Closure $httpAfterRequest = null;

    /**
     * 发起一次 HTTP 请求;失败不抛异常,而是返回带 error 的结果数组。
     *
     * @param string $method  HTTP 方法,GET/POST/PUT/PATCH/DELETE/HEAD/OPTIONS(大小写不限)
     * @param string $url      请求地址
     * @param array  $options  可选项:headers、query、json、form_params、multipart、body、bearer、
     *                         auth、timeout、connect_timeout、retry、verify、proxy
     * @return array{ok:bool,status:int,headers:array,body:string,json:mixed,method:string,url:string,duration_ms:int,error:string}
     */
    public static function httpRequest(string $method, string $url, array $options = []): array
    {
        $method = strtoupper($method);
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'], true)) {
            throw new \InvalidArgumentException("不支持的 HTTP 方法：{$method}");
        }

        $startedAt = microtime(true);

        try {
            $response = class_exists(\GuzzleHttp\Client::class)
                ? self::requestWithGuzzle($method, $url, $options)
                : self::requestWithStream($method, $url, $options);

            $result = $response + [
                'method' => $method,
                'url' => $url,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'error' => '',
            ];
        } catch (\Throwable $throwable) {
            $result = [
                'ok' => false,
                'status' => 0,
                'headers' => [],
                'body' => '',
                'json' => null,
                'method' => $method,
                'url' => $url,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'error' => $throwable->getMessage(),
            ];
        }

        self::httpNotifyAfterRequest($result);

        return $result;
    }

    /** 触发出站请求回调,回调自身的异常不影响业务 */
    private static function httpNotifyAfterRequest(array $result): void
    {
        if (self::$httpAfterRequest !== null) {
            try {
                (self::$httpAfterRequest)($result);
            } catch (\Throwable) {
                // 日志回调失败静默
            }
        }
    }

    /**
     * GET 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpGet(string $url, array $options = []): array
    {
        return self::httpRequest('GET', $url, $options);
    }

    /**
     * POST 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()(常用 json/form_params/multipart)
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpPost(string $url, array $options = []): array
    {
        return self::httpRequest('POST', $url, $options);
    }

    /**
     * PUT 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpPut(string $url, array $options = []): array
    {
        return self::httpRequest('PUT', $url, $options);
    }

    /**
     * PATCH 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpPatch(string $url, array $options = []): array
    {
        return self::httpRequest('PATCH', $url, $options);
    }

    /**
     * DELETE 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpDelete(string $url, array $options = []): array
    {
        return self::httpRequest('DELETE', $url, $options);
    }

    /**
     * HEAD 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpHead(string $url, array $options = []): array
    {
        return self::httpRequest('HEAD', $url, $options);
    }

    /**
     * OPTIONS 请求。
     *
     * @param string $url     请求地址
     * @param array  $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpRequest()
     */
    public static function httpOptions(string $url, array $options = []): array
    {
        return self::httpRequest('OPTIONS', $url, $options);
    }

    /**
     * 流式请求:响应到达一块回调一块,不等整个响应结束。
     *
     * @param string   $method  HTTP 方法
     * @param string   $url     请求地址
     * @param callable $onChunk 分块回调 fn(string $chunk): mixed,返回 false 中断接收
     * @param array    $options 可选项,同 httpRequest()
     * @return array{ok:bool,status:int,body:string,error:string,duration_ms:int} body 为完整内容
     */
    public static function httpStream(string $method, string $url, callable $onChunk, array $options = []): array
    {
        $startedAt = microtime(true);

        try {
            $result = class_exists(\GuzzleHttp\Client::class)
                ? self::streamWithGuzzle(strtoupper($method), $url, $options, $onChunk)
                : self::streamWithNative(strtoupper($method), $url, $options, $onChunk);

            $result = $result + [
                'method' => strtoupper($method),
                'url' => $url,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'error' => '',
            ];
        } catch (\Throwable $throwable) {
            $result = [
                'ok' => false,
                'status' => 0,
                'body' => '',
                'method' => strtoupper($method),
                'url' => $url,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'error' => $throwable->getMessage(),
            ];
        }

        self::httpNotifyAfterRequest($result);

        return $result;
    }

    /**
     * SSE 流式请求:按事件解析 text/event-stream,适配 DeepSeek/OpenAI 等 AI 接口。
     *
     * @param string   $method  HTTP 方法
     * @param string   $url     请求地址
     * @param callable $onEvent 事件回调 fn(string $data): mixed,每个 data: 负载回调一次
     *                          (自动跳过 [DONE]),返回 false 中断接收
     * @param array    $options 可选项,同 httpRequest()
     * @return array 结果数组,结构同 httpStream()
     */
    public static function httpSse(string $method, string $url, callable $onEvent, array $options = []): array
    {
        $buffer = '';

        return self::httpStream($method, $url, static function (string $chunk) use (&$buffer, $onEvent) {
            $buffer .= $chunk;

            while (($pos = strpos($buffer, "\n\n")) !== false) {
                $rawEvent = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 2);

                $data = [];
                foreach (explode("\n", str_replace("\r", '', $rawEvent)) as $line) {
                    if (str_starts_with($line, 'data:')) {
                        $data[] = ltrim(substr($line, 5));
                    }
                }

                if ($data === []) {
                    continue;
                }

                $payload = implode("\n", $data);
                if ($payload === '[DONE]') {
                    continue;
                }

                if ($onEvent($payload) === false) {
                    return false;
                }
            }

            return true;
        }, $options);
    }

    private static function streamWithGuzzle(string $method, string $url, array $options, callable $onChunk): array
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => $options['timeout'] ?? 300,
            'connect_timeout' => $options['connect_timeout'] ?? 5,
            'verify' => $options['verify'] ?? true,
            'proxy' => $options['proxy'] ?? null,
        ]);

        $requestOptions = self::buildGuzzleOptions($options) + ['stream' => true];
        $response = $client->request(strtoupper($method), self::appendQuery($url, $options['query'] ?? []), $requestOptions);

        $body = $response->getBody();
        $full = '';
        while (!$body->eof()) {
            $chunk = $body->read(8192);
            if ($chunk === '') {
                continue;
            }
            $full .= $chunk;
            if ($onChunk($chunk) === false) {
                break;
            }
        }

        return [
            'ok' => $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
            'status' => $response->getStatusCode(),
            'body' => $full,
        ];
    }

    private static function streamWithNative(string $method, string $url, array $options, callable $onChunk): array
    {
        $headers = $options['headers'] ?? [];
        $body = self::streamBody($headers, $options);
        $url = self::appendQuery($url, $options['query'] ?? []);

        if (isset($options['bearer'])) {
            $headers['Authorization'] = 'Bearer ' . $options['bearer'];
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => self::formatHeaders($headers),
                'content' => $body,
                'timeout' => $options['timeout'] ?? 300,
                'ignore_errors' => true,
            ],
        ]);

        $stream = fopen($url, 'r', false, $context);
        if ($stream === false) {
            throw new \RuntimeException('流式请求建立连接失败');
        }

        $status = self::statusFromHeaders($http_response_header ?? []);
        $full = '';
        while (!feof($stream)) {
            $chunk = fread($stream, 8192);
            if ($chunk === false || $chunk === '') {
                continue;
            }
            $full .= $chunk;
            if ($onChunk($chunk) === false) {
                break;
            }
        }
        fclose($stream);

        return [
            'ok' => $status >= 200 && $status < 300,
            'status' => $status,
            'body' => $full,
        ];
    }

    private static function requestWithGuzzle(string $method, string $url, array $options): array
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => $options['timeout'] ?? 10,
            'connect_timeout' => $options['connect_timeout'] ?? 5,
            'verify' => $options['verify'] ?? true,
            'proxy' => $options['proxy'] ?? null,
        ]);

        $requestOptions = self::buildGuzzleOptions($options);
        $attempts = max(1, (int) ($options['retry'] ?? 0) + 1);
        $last = null;

        for ($i = 0; $i < $attempts; $i++) {
            try {
                $response = $client->request($method, self::appendQuery($url, $options['query'] ?? []), $requestOptions);
                $body = (string) $response->getBody();

                return [
                    'ok' => $response->getStatusCode() >= 200 && $response->getStatusCode() < 300,
                    'status' => $response->getStatusCode(),
                    'headers' => $response->getHeaders(),
                    'body' => $body,
                    'json' => self::decodeJson($body),
                ];
            } catch (\Throwable $throwable) {
                $last = $throwable;
            }
        }

        throw $last ?? new \RuntimeException('请求失败');
    }

    private static function buildGuzzleOptions(array $options): array
    {
        $requestOptions = [
            'headers' => $options['headers'] ?? [],
            'http_errors' => false,
        ];

        if (isset($options['bearer'])) {
            $requestOptions['headers']['Authorization'] = 'Bearer ' . $options['bearer'];
        }

        if (isset($options['auth'])) {
            $requestOptions['auth'] = $options['auth'];
        }

        foreach (['json', 'form_params', 'multipart', 'body'] as $key) {
            if (array_key_exists($key, $options)) {
                $requestOptions[$key] = $options[$key];
            }
        }

        return $requestOptions;
    }

    private static function requestWithStream(string $method, string $url, array $options): array
    {
        $headers = $options['headers'] ?? [];
        $body = self::streamBody($headers, $options);
        $url = self::appendQuery($url, $options['query'] ?? []);

        if (isset($options['bearer'])) {
            $headers['Authorization'] = 'Bearer ' . $options['bearer'];
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => self::formatHeaders($headers),
                'content' => $body,
                'timeout' => $options['timeout'] ?? 10,
                'ignore_errors' => true,
            ],
        ]);

        $content = file_get_contents($url, false, $context);
        $responseHeaders = $http_response_header ?? [];
        $status = self::statusFromHeaders($responseHeaders);

        return [
            'ok' => $status >= 200 && $status < 300,
            'status' => $status,
            'headers' => $responseHeaders,
            'body' => $content === false ? '' : $content,
            'json' => self::decodeJson($content === false ? '' : $content),
        ];
    }

    private static function streamBody(array &$headers, array $options): string
    {
        if (array_key_exists('json', $options)) {
            $headers['Content-Type'] = $headers['Content-Type'] ?? 'application/json';
            return json_encode($options['json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        }

        if (array_key_exists('form_params', $options)) {
            $headers['Content-Type'] = $headers['Content-Type'] ?? 'application/x-www-form-urlencoded';
            return http_build_query($options['form_params']);
        }

        if (array_key_exists('body', $options)) {
            return (string) $options['body'];
        }

        return '';
    }

    private static function appendQuery(string $url, array $query): string
    {
        if ($query === []) {
            return $url;
        }

        return $url . (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
    }

    private static function formatHeaders(array $headers): string
    {
        $lines = [];
        foreach ($headers as $name => $value) {
            $lines[] = $name . ': ' . $value;
        }

        return implode("\r\n", $lines);
    }

    private static function statusFromHeaders(array $headers): int
    {
        $first = $headers[0] ?? '';
        if (preg_match('/\\s(\\d{3})\\s/', $first, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private static function decodeJson(string $body): mixed
    {
        $decoded = json_decode($body, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
