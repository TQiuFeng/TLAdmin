# TLAdmin 部署文档

> 适用版本:server(PHP 8.2+)+ web(Node 20+ 构建)。Author: qiufeng

## 1. 环境要求

| 组件 | 版本 | 用途 |
|------|------|------|
| PHP | >= 8.2(扩展:pdo_mysql、redis、mongodb、curl、openssl、mbstring;行政区划需 pdo_sqlite,二维码需 gd) | 后端运行时 |
| MySQL | >= 8.0 | 业务数据 |
| Redis | >= 6 | token / 限流 / 队列 / 上传票据 |
| MongoDB | >= 6 | 操作 / 登录 / 第三方请求日志 |
| Node.js | >= 20 | 前端构建(仅构建机需要) |
| Nginx | >= 1.20 | 反向代理与静态资源 |

## 2. 后端部署

```bash
cd server
composer install --no-dev --optimize-autoloader

# 配置环境
cp .env.example .env
# 修改 .env:数据库、Redis、MongoDB,APP_ENV 改为 production
php bin/console key:generate  # 生成随机 APP_KEY 写入 .env;生产环境没有 APP_KEY 会直接报错

# 初始化数据库
php bin/console migrate
php bin/console seed          # 默认账号 admin / admin123456,部署后立即改密

# 预热路由缓存(可选,首个请求也会自动生成)
php bin/console route:cache
```

PHP-FPM 站点根目录指向 `server/public`,所有请求重写到 `index.php`。

## 3. 前端部署

```bash
cd web
npm ci
npm run build                 # 产物在 web/dist/
```

把 `dist/` 部署到 Nginx 静态目录(如 `/var/www/tladmin-admin`),
或复制到 `server/public/admin/`。

## 4. Nginx 配置示例

```nginx
server {
    listen 80;
    server_name admin.example.com;

    # 前端 SPA
    root /var/www/tladmin-admin;
    index index.html;
    location / {
        try_files $uri $uri/ /index.html;   # history 路由回退
    }

    # 后端 API
    location /adminapi {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME /var/www/tladmin/server/public/index.php;
    }

    # 本地磁盘附件(使用云存储直传时可去掉)
    location /storage {
        alias /var/www/tladmin/server/public/storage;
    }
}
```

> 部署在反向代理后,记得在「系统配置 → IP 归属地屏蔽」开启"信任代理头",
> 否则客户端 IP 取到的是代理地址。

## 5. 定时任务与队列

```bash
# 系统 crontab(每分钟调度一次,到期任务才会执行,Redis 锁防止多机重复)
* * * * * cd /var/www/tladmin/server && php bin/console schedule:run >> runtime/log/schedule.log 2>&1

# 队列常驻进程(用 systemd / supervisor 守护)
php bin/console queue:work
```

systemd 单元示例(`/etc/systemd/system/tladmin-queue.service`):

```ini
[Unit]
Description=TLAdmin Queue Worker
After=redis.service

[Service]
WorkingDirectory=/var/www/tladmin/server
ExecStart=/usr/bin/php bin/console queue:work
Restart=always
User=www-data

[Install]
WantedBy=multi-user.target
```

## 6. 上线检查清单

- [ ] 已执行 `php bin/console key:generate`,`.env` 的 `APP_KEY` 是随机串(用于加密第三方密钥和动态验证码)
- [ ] 从旧版本升级:执行 `php bin/console secrets:reencrypt`,把旧版用默认密钥加密的第三方密钥换成当前 APP_KEY
- [ ] `.env` 不要提交到仓库(已在 .gitignore)
- [ ] admin 默认密码已修改;按需开启动态验证码全局开关
- [ ] MySQL / Redis / MongoDB 不对公网暴露
- [ ] 附件存储:云存储直传需在「附件管理 → 第三方配置」填入密钥(自动加密存储)
- [ ] OpenAPI 文档地址 `/api-docs.html` 视情况限制访问
- [ ] crontab 与队列进程已配置并验证(`php bin/console queue:size`)
- [ ] 日志保留策略默认 90 天(`config/schedule.php` 可调)

## 7. 常用运维命令

```bash
php bin/console migrate:status    # 迁移状态
php bin/console route:list        # 全部路由与权限标识
php bin/console schedule:list     # 定时任务表
php bin/console queue:size        # 队列积压
php bin/console gen:crud tl_xxx --title=业务名   # 生成 CRUD 全套代码
```
