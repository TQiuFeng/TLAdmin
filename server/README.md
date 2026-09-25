# TLAdmin Server

TLAdmin 后端是管理后台 API 层，当前实现采用 ThinkPHP 生态依赖、think-orm、自研轻量 HTTP 内核和注解路由。它的目标不是只提供几个 CRUD 接口，而是为后台系统提供认证、权限、配置、日志、上传、OpenAPI、第三方能力配置等通用基础设施。

## 后端特点

- 统一响应：所有管理端接口默认返回 `{code,message,data,request_id,timestamp}`，支持 JSON/XML 双格式。
- 注解路由：控制器类使用 `#[RestController(prefix, tag)]`，方法使用 `#[GetMapping]`、`#[PostMapping]`、`#[PutMapping]`、`#[DeleteMapping]`。
- 文档同源：路由注解、权限标识、响应 VO 会被 OpenAPI 生成器读取，减少接口文档和真实代码不一致。
- RBAC 权限：管理员、角色、菜单、按钮、接口权限统一建模，权限中间件以后端为准做校验。
- Token 体系：access token + refresh token 存 Redis，支持退出、刷新、修改密码后全端下线。
- 安全配置：支持登录失败锁定、TOTP 动态验证码、接口限流、IP 归属地屏蔽、敏感字段脱敏和配置密钥加密。
- 附件直传：后端只签发直传凭证，前端直传云端，本地磁盘作为兜底，附件统一登记入库。
- 第三方能力：支付、微信公众号、短信、存储都通过统一 Service 和配置 schema 暴露，不让业务代码直接散落调用 SDK。
- 本地数据能力：手机号归属地、IP 归属地通过本地 CSV 查询，适合安全策略和运营工具。

## 运行环境

```text
PHP >= 8.1
Composer
MySQL 8 或 MariaDB 兼容版本
Redis
MongoDB，可用于登录日志和操作日志
```

`.env.example` 提供了默认配置项：

```text
APP_NAME=TLAdmin
APP_ENV=local
APP_DEBUG=true
APP_KEY=
API_RESPONSE_FORMAT=json
DB_HOST=127.0.0.1
DB_DATABASE=tladmin
REDIS_HOST=127.0.0.1
MONGO_HOST=127.0.0.1
```

生产环境必须设置真实 `APP_KEY`、数据库账号、Redis 密码、MongoDB 账号，并关闭 `APP_DEBUG`。

## 本地启动

```bash
composer install
cp .env.example .env
# 修改 .env 中 DB_*、REDIS_*、MONGO_*,然后生成 APP_KEY:
# php bin/console key:generate
php bin/console migrate
php bin/console seed
# 注意 PHP_CLI_SERVER_WORKERS=4:php -S 默认单进程,一次只处理一个请求,
# 后台页面并发拉多个接口时会排队,看起来"接口很慢"。详见下方"接口耗时排查"。
PHP_CLI_SERVER_WORKERS=4 php -S 127.0.0.1:8000 -t public public/router.php
```

默认初始化账号：

```text
admin / admin123456
```

生产环境部署后必须立即修改默认密码，并建议启用动态验证码。

## CLI 命令

```bash
php bin/console migrate          # 执行未应用的迁移
php bin/console migrate:status   # 查看迁移状态
php bin/console seed             # 执行初始化数据
php bin/console route:list       # 查看注解路由、权限和摘要
php bin/console route:cache      # 重建路由缓存
php bin/console schedule:list    # 查看定时任务表(config/schedule.php)
php bin/console schedule:run     # 执行到期定时任务,配合系统 crontab 每分钟调用
php bin/console queue:work       # 队列消费(--once 只消费一条)
php bin/console queue:size       # 查看队列积压
php bin/console gen:crud 表名 --title=名称   # 生成 CRUD 全套代码(后端+前端+菜单种子)
```

所有数据库结构变更必须进入 `database/migrations`，初始化数据进入 `database/seeders`。

## 接口调试台

启动后打开：

```text
http://127.0.0.1:8000/api-docs.html
```

登录接口返回 `access_token` 后，在 Swagger UI 右上角 `Authorize` 中填写：

```text
Bearer <access_token>
```

OpenAPI 原始文档地址：

```text
GET /adminapi/openapi.json
```

调试 JSON/XML 返回格式：

```text
GET /adminapi/health?format=json
GET /adminapi/health?format=xml
Accept: application/json
Accept: application/xml
```

## 主要接口

认证：

```text
POST /adminapi/auth/login              登录，参数 username/password/code
POST /adminapi/auth/refresh            刷新 token，参数 refresh_token
POST /adminapi/auth/logout             退出登录
GET  /adminapi/auth/profile            当前用户、角色、权限点、菜单树
POST /adminapi/auth/password           修改自己的密码
GET  /adminapi/auth/totp/status        动态验证码绑定状态
POST /adminapi/auth/totp/setup         生成绑定密钥和 otpauth URI
POST /adminapi/auth/totp/confirm       确认绑定动态验证码
POST /adminapi/auth/totp/disable       解绑动态验证码
```

系统管理：

```text
GET/POST     /adminapi/system/users
GET/PUT/DEL  /adminapi/system/users/{id}
POST         /adminapi/system/users/{id}/reset-password
POST         /adminapi/system/users/{id}/reset-totp

GET/POST     /adminapi/system/roles
GET/PUT/DEL  /adminapi/system/roles/{id}
GET          /adminapi/system/roles/all

GET/POST     /adminapi/system/menus
PUT/DEL      /adminapi/system/menus/{id}

GET/POST     /adminapi/system/depts
PUT/DEL      /adminapi/system/depts/{id}

GET/POST     /adminapi/system/posts
PUT/DEL      /adminapi/system/posts/{id}
GET          /adminapi/system/posts/all

GET/POST     /adminapi/system/dict/types
PUT/DEL      /adminapi/system/dict/types/{id}
GET/POST     /adminapi/system/dict/data
PUT/DEL      /adminapi/system/dict/data/{id}
GET          /adminapi/dicts/{code}
```

配置、安全和工具：

```text
GET/POST /adminapi/system/config/api-response-format
GET/POST /adminapi/system/config/security-totp
GET/POST /adminapi/system/config/security-rate-limit
GET/POST /adminapi/security/ip-block/config
GET      /adminapi/security/ip-block/check
GET      /adminapi/tools/phone-location
GET      /adminapi/tools/ip-location
```

附件和第三方能力：

```text
POST   /adminapi/attachments/policy
POST   /adminapi/attachments
POST   /adminapi/attachments/upload
GET    /adminapi/attachments
DELETE /adminapi/attachments/{id}
GET    /adminapi/attachments/{id}/url

GET    /adminapi/integrations
GET    /adminapi/integrations/config?group=storage
POST   /adminapi/integrations/config
```

日志（MongoDB）：

```text
GET /adminapi/system/logs/operations    操作日志（写操作，敏感字段脱敏）
GET /adminapi/system/logs/logins        登录日志
GET /adminapi/system/logs/third-party   第三方请求日志（Tools::httpXxx 出站请求自动记录）
```

数据权限：角色 `data_scope` 支持 `all` / `dept_tree` / `dept` / `custom` / `self`，
管理员列表按操作者全部角色的作用域取并集过滤（超管不受限）。

## 动态验证码流程

动态验证码使用 Google Authenticator 兼容的 TOTP。

1. 管理员开启系统配置 `security-totp`。
2. 用户登录后请求 `POST /adminapi/auth/totp/setup` 生成密钥和 `otpauth_uri`。
3. 前端把 `otpauth_uri` 渲染成二维码，用户用验证器扫码。
4. 用户输入 6 位动态码，请求 `POST /adminapi/auth/totp/confirm`。
5. 后续登录时如果后端返回 `data.totp_required=true`，前端追加 `code` 字段重新登录。
6. 验证器丢失时，拥有 `system:user:update` 权限的管理员可调用 `POST /adminapi/system/users/{id}/reset-totp` 强制重置。

## 附件直传流程

```text
1. POST /adminapi/attachments/policy {filename, size, mime}
   返回 {disk, mode, key, host, form, expires_in}

2. mode=direct
   前端把 form 字段和 file 以 multipart 表单 POST 到 host，文件直接进入 OSS/COS/七牛。

3. mode=server
   本地磁盘兜底，把 key 和 file POST 到 /adminapi/attachments/upload，服务端接收并登记。

4. 云端直传完成后
   POST /adminapi/attachments {key} 登记入库，返回附件记录。
```

附件安全规则：

- 扩展名白名单来自 `upload.allowed_exts`。
- 大小上限来自 `upload.max_size_mb`。
- 直传凭证含一次性票据，默认依赖 Redis 保存。
- 私有桶文件通过 `GET /adminapi/attachments/{id}/url` 获取临时签名 URL。
- 云端密钥以 `enc:v1:` 前缀加密存储，接口返回时脱敏。

## 目录约定

```text
app/adminapi/controller     控制器，只做参数读取、调用 Service、返回响应
app/adminapi/middleware     认证、权限、限流等中间件
app/adminapi/vo             响应 VO，供 OpenAPI schema 使用
app/common/service          业务 Service，按 auth/system/log/integration/geo/security 分组
app/common/support          工具类 Date/Str/Arr/Money/Http/Random/Uuid/Crypto/File/Pinyin
app/common/database         数据库引导、迁移执行器、Mongo 连接
app/common/router           注解路由和路由扫描器
app/common/response         统一响应、格式解析、JSON/XML formatter
app/common/queue            Redis 队列（Queue::push / queue:work，延迟、重试、失败日志）
app/common/schedule         定时调度（cron 表达式解析 + Redis 同分钟去重）
app/common/generator        CRUD 代码生成器
app/job                     队列任务与定时任务实现
config                      api/security/integration/schedule 等配置
database/migrations         迁移
database/seeders            种子
public                      入口文件、开发路由器、Swagger UI
resources/geo               本地归属地 CSV 数据
bin/console                 CLI 入口
```

部署文档见 `../docs/tladmin-deploy.md`（Nginx、crontab、队列守护、上线清单）。

## 接口耗时排查

发现某个接口"慢"(几十到一百多毫秒)时,先按下面这张表对号入座,**多数情况下不是框架代码的问题**:

| 现象 | 真实原因 | 处理 |
| --- | --- | --- |
| 后台页面一打开,多个接口都显示 100ms+ | `php -S` 是**单进程**,并发请求排队;排在后面的接口耗时 = 自己耗时 + 前面所有请求耗时 | 开发期用 `PHP_CLI_SERVER_WORKERS=4 php -S ...` 多 worker;生产走 php-fpm 进程池,天然并行,无此问题 |
| `/adminapi/auth/login` 比其它接口慢 50~100ms | bcrypt 密码校验**故意设计成慢**(安全特性),Spring Security 同强度一样慢 | 正常,无需优化;别为提速降低 bcrypt cost |
| 所有接口都比预期慢一截(含无业务逻辑的) | **opcache 未开**时 PHP 每请求重新编译 vendor+框架数百个文件 | 生产开 opcache(只是字节码缓存,非 JIT);并 `composer dump-autoload --optimize --classmap-authoritative` |

实测参考(`php -S` 本地、`APP_DEBUG=false`):`/adminapi/auth/profile` 单请求服务端耗时 **约 12ms(p50)**;同样接口并发 10 个,单进程下最慢 150ms、4 workers 下最慢 55ms——差距全在排队,不在业务代码。

> 量接口耗时要看**中位数**(压几十上百次),单个请求数字受磁盘缓存冷热影响,波动十几毫秒属正常,不能据此判断快慢。

框架层已做的两项优化(无需开任何扩展):
- **路由缓存**:`APP_DEBUG=false` 时 `RouteScanner` 直接信任 `runtime/cache/routes.php`,跳过逐文件 `filemtime` 探测;改了控制器注解后需删缓存文件或临时置 `APP_DEBUG=true` 重建。
- **日志后置**:`public/index.php` 中操作日志、第三方请求日志在 `fastcgi_finish_request()`(响应发出、连接断开)之后才写,不计入接口耗时——仅 php-fpm 生效,`php -S` 下自动跳过。

## 新增接口规范

新增后端接口时按以下顺序处理：

1. 在 `app/adminapi/vo` 新增或复用响应 VO，并给字段补 `#[ApiField]`。
2. 在对应 Controller 方法上声明 `#[GetMapping]`、`#[PostMapping]` 等注解。
3. 写清 `summary`、`permission`、`message`、`listOf` 或 `response`。
4. 业务逻辑放到 `app/common/service`，Controller 不写复杂事务。
5. 新权限点写入菜单权限体系，或由后续代码生成器统一写入。
6. 运行 `php bin/console route:list` 检查路由和权限。
7. 打开 `api-docs.html` 检查 OpenAPI schema 是否可读。

示例：

```php
#[GetMapping(permission: 'system:user:list', summary: '管理员列表', listOf: AdminUserVo::class)]
public function index(): PageVo
{
    return PageVo::of($this->service->paginate($filters, $page, $pageSize), AdminUserVo::class);
}
```
