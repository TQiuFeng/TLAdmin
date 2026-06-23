# TLAdmin

TLAdmin 是一个前后端分离的中后台管理框架，用来快速搭建后台系统的基础盘：登录、权限、菜单、配置、日志、上传、接口文档、第三方能力配置和常用工具都已经内置。

它解决的核心问题不是“生成几个 CRUD 页面”，而是把每个后台项目都会反复踩的基础问题先处理好，让后续业务模块直接往上接。

## 解决的痛点

### 1. 每个后台都要重复搭基础架子

普通后台项目一开始就要做登录、token、管理员、角色、菜单、按钮权限、部门、岗位、字典、系统配置、操作日志、登录日志。  
TLAdmin 已经把这些基础模块放进后端，业务项目可以直接在这个权限和配置体系上开发。

### 2. 权限容易前后端不一致

很多项目只在前端隐藏按钮，但后端接口没有真正拦住，或者菜单、按钮、接口权限分散维护。  
TLAdmin 把菜单、按钮、接口权限统一成一套 RBAC 模型，前端根据后端菜单树生成路由，后端根据接口权限做最终校验。

### 3. 接口文档经常和真实代码脱节

手写接口文档很容易过期，调试时还要单独找 token、拼参数。  
TLAdmin 使用控制器注解和响应 VO 生成 OpenAPI 文档，接口、权限、返回结构来自同一份代码，并内置 Swagger UI 调试台。

### 4. API 返回格式和错误处理不统一

不同接口各写各的返回结构，前端请求层就会越来越乱。  
TLAdmin 后端统一返回 `code/message/data/request_id/timestamp`，并支持 JSON/XML 双格式；前端统一处理 token、刷新 token、401、403、错误提示、上传和下载。

### 5. 上传和对象存储接入成本高

文件上传如果都走后端，会占服务器带宽；直接接 OSS/COS/七牛又会把密钥和 SDK 逻辑散落到业务里。  
TLAdmin 采用后端签发上传凭证、前端直传云端的方式，本地磁盘作为兜底，并统一登记附件记录。

### 6. 第三方能力配置容易混乱

支付、公众号、短信、OSS 如果直接在业务代码里调用 SDK，后面换渠道、查问题、保护密钥都会很麻烦。  
TLAdmin 把支付、微信公众号、短信、存储统一放到 Service 和配置 schema 里，密钥加密存储，接口返回脱敏。

### 7. 排查问题缺少链路

后台系统出问题时，如果没有 request_id、登录日志、操作日志，很难定位是谁、什么时候、请求了什么。  
TLAdmin 每次请求带 `request_id`，并提供登录日志、操作日志和敏感字段脱敏。

### 8. 常用工具到处重复写

日期、金额、字符串、数组、随机数、UUID、HTTP 请求、文件、拼音这些工具如果每个模块临时写，会导致行为不一致。  
TLAdmin 后端和前端都提供了统一工具库，业务代码可以直接复用。

## 当前已经具备

### 后端

- **认证**：登录、退出、刷新 token、修改密码、当前用户信息；access + refresh token 存 Redis；登录失败锁定。
- **动态验证码**：Google Authenticator（TOTP）二次验证，全局开关 + 个人绑定/解绑。
- **RBAC 权限**：管理员、角色、菜单（目录/菜单/按钮/接口四类节点），按钮与接口权限统一校验，超管绕过。
- **数据权限**：角色 `data_scope`（全部/本部门及以下/本部门/自定义部门/仅本人）自动过滤列表数据。
- **系统模块**：部门（树）、岗位、字典（类型 + 数据）、系统配置。
- **会员体系**：C 端会员（`tl_user`）后台管理 + C 端便捷函数（注册、登录、取信息、改手机号、改密码，独立 token，见手册）。
- **日志（MongoDB）**：操作日志、登录日志、第三方请求日志，敏感字段脱敏，支持 request_id 检索。
- **安全**：接口限流（按用户/IP 每秒计数）、IP 归属地屏蔽（国家/省/市/区县级联选择）。
- **附件**：前端直传 OSS/COS/七牛 + 本地磁盘兜底 + 一次性上传票据 + 私有桶临时访问 URL。
- **第三方能力**：支付、微信公众号、短信、存储统一 Service + 配置 schema，密钥加密存储、返回脱敏，后台可视化配置。
- **任务**：定时任务（cron 表达式 + Redis 同分钟去重）、Redis 队列（即时/延迟、自动重试、失败日志）。
- **代码生成器**：从数据表 + 字段配置生成后端 VO/Service/Controller + 前端页面 + 菜单种子，支持生成前代码预览（CLI 与后台可视化两种入口）。
- **OpenAPI**：控制器注解自动生成文档 + Swagger UI 调试台，接口/权限/返回结构来自同一份代码。
- **工具库**：统一 HTTP 客户端、日期、金额、字符串、数组、加密、文件、拼音、二维码、邮件、敏感词、五级行政区划、手机号/IP 归属地等（见工具手册）。
- **CLI**：迁移、种子、路由、定时调度、队列、代码生成。

### 前端（Vue3 + TDesign）

- 登录页（TDesign 官方风格，支持动态验证码）、按后端菜单树生成的动态路由、`v-permission` 按钮权限指令。
- 后台布局：左侧菜单、顶部栏、面包屑、多标签页、全屏、个人中心、退出登录。
- 系统管理页：管理员、角色、菜单、部门、岗位、字典、系统配置、操作/登录/第三方日志、附件、插件配置。
- 会员管理、代码生成器（可视化字段配置 + 代码预览）、个人中心（改密码 / TOTP 绑定）。
- 通用组件：`TablePlus`（分页表格）、`FormDialog`（弹窗表单）、`UploadPlus`（直传）、`v-permission`（按钮权限）。
- 统一请求封装：自动带 token、401 刷新 token 重放、403 提示、统一错误提示、上传、blob 下载。
- 工具库：`request`、`date`、`string`、`array`、`money`、`file`、`random`、`uuid`、`token`。
- 全局时间格式：后台可配置，前端所有时间统一按该格式渲染。

## 技术栈

```text
后端：PHP 8.2+、ThinkPHP 生态、think-orm、MySQL 8、Redis、MongoDB
前端：Vue3、Vite、TypeScript、Pinia、Vue Router、Axios、TDesign Vue Next、dayjs、lodash-es
第三方：yansongda/pay、EasyWeChat、EasySms、Guzzle、Carbon、pragmarx/google2fa、OSS/COS/七牛 SDK
```

## 项目结构

```text
TLAdmin/
  server/                       后端
    app/
      adminapi/controller/      控制器(注解路由,后台 /adminapi 与 C 端 /api 均在此扫描)
      adminapi/vo/              响应 VO(ApiField 注解 → OpenAPI schema)
      adminapi/middleware/      认证、限流、权限中间件
      common/service/           业务 Service(auth/system/member/log/integration/geo/security)
      common/support/           工具库统一入口 Tools::xxx()
      common/queue/             Redis 队列(Queue::push + queue:work)
      common/schedule/          定时调度(cron 解析 + schedule:run)
      common/generator/         代码生成器
      job/                      队列任务与定时任务实现
    database/migrations|seeders 迁移与种子
    bin/console                 CLI 入口
  web/                          前端管理后台
    src/api/                    接口请求(system/member/gen)
    src/components/             通用组件(TablePlus/FormDialog/UploadPlus)
    src/pages/                  页面
    src/utils/                  前端工具库
  docs/                         文档(见下方「文档」)
```

## 文档

| 文档 | 内容 |
|------|------|
| `docs/tladmin-helper-manual.md` | 工具类、服务层便捷函数、前端工具库与组件的使用手册（带签名和实测示例） |
| `docs/tladmin-deploy.md` | 部署文档（Nginx、crontab、队列守护、上线清单） |
| `docs/tladmin-thinkphp8-admin-design.md` | 设计文档（需求权威） |
| `server/README.md` | 后端说明与接口清单 |
| `web/README.md` | 前端说明 |

## 本地启动

后端：

```bash
cd server
composer install
cp .env.example .env
php bin/console migrate
php bin/console seed
php -S 127.0.0.1:8000 -t public public/router.php
```

默认账号：

```text
admin / admin123456
```

前端：

```bash
cd web
npm install
npm run dev
```

访问地址：

```text
前端：http://127.0.0.1:5173
后端：http://127.0.0.1:8000
接口调试台：http://127.0.0.1:8000/api-docs.html
```

## 两套 API

- **后台 API `/adminapi/*`**：管理后台用，走 access token + RBAC 权限校验。
- **C 端 API `/api/*`**：小程序/App/H5 用，独立的会员 token（与后台隔离），不走后台 RBAC。例如会员注册、登录、个人资料等。

## 常用命令

```bash
cd server
php bin/console migrate          # 执行未应用的迁移
php bin/console migrate:status   # 迁移状态
php bin/console seed             # 初始化/补充种子数据
php bin/console route:list       # 查看全部注解路由与权限
php bin/console route:cache      # 重建路由缓存
php bin/console schedule:list    # 查看定时任务表
php bin/console schedule:run     # 执行到期定时任务(配合系统 crontab 每分钟调用)
php bin/console queue:work       # 队列消费(--once 只消费一条)
php bin/console queue:size       # 查看队列积压
php bin/console gen:crud 表名 --title=名称   # 生成 CRUD 全套代码

cd web
npm run dev                      # 开发
npm run build                    # 类型检查 + 生产构建
```

## 后续可扩展

- 支付、微信公众号、短信的完整业务闭环（下单、回调、消息）。
- 会员手机验证码登录、第三方登录绑定。
- 自动化测试套件（PHPUnit + Vitest）与 CI/CD。
- 业务模块按需用代码生成器快速生成。
# TLAdmin
# TLAdmin
# TLAdmin
