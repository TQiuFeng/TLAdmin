<?php

/**
 * 演示中心:把代码生成器生成的演示菜单收进"演示中心"目录,并给空的演示表填一些样例数据。
 *
 * 文件名以 sample_ 开头,排在 gen_*_menu 之后执行,保证演示菜单已经存在;整体幂等,可重复执行。
 * Author: qiufeng
 */

use think\facade\Db;

return [
    'run' => static function (): bool {
        $changed = false;
        $now = time();
        $day = 86400;

        // ---- 1. 演示中心目录 ----
        $catalog = Db::table('tl_admin_menu')->where('type', 'catalog')->where('path', '/demo')->find();
        if ($catalog) {
            $catalogId = (int) $catalog['id'];
        } else {
            $catalogId = (int) Db::table('tl_admin_menu')->insertGetId([
                'parent_id' => 0, 'type' => 'catalog', 'title' => '演示中心',
                'name' => 'Demo', 'path' => '/demo', 'component' => '',
                'icon' => 'app', 'permission' => '', 'sort' => 90,
                'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
            ]);
            $changed = true;
        }

        // ---- 2. 工具箱(手写页面,展示内置 Tools 工具库) ----
        if (!Db::table('tl_admin_menu')->where('permission', 'demo-toolbox:use')->find()) {
            Db::table('tl_admin_menu')->insert([
                'parent_id' => $catalogId, 'type' => 'menu', 'title' => '工具箱',
                'name' => 'DemoToolbox', 'path' => '/demo/toolbox', 'component' => 'demo/toolbox/index',
                'icon' => 'tools', 'permission' => 'demo-toolbox:use', 'sort' => 0,
                'visible' => 1, 'status' => 1, 'create_time' => $now, 'update_time' => $now,
            ]);
            $changed = true;
        }

        // ---- 3. 生成器生成的演示菜单挂到目录下,排在工具箱之后 ----
        $demos = ['demo-product', 'demo-article', 'demo-customer', 'demo-order', 'demo-notice'];
        foreach ($demos as $i => $key) {
            $sort = $i + 1;
            $moved = Db::table('tl_admin_menu')
                ->where('permission', "{$key}:list")
                ->where(static function ($query) use ($catalogId, $sort): void {
                    $query->where('parent_id', '<>', $catalogId)->whereOr('sort', '<>', $sort);
                })
                ->update(['parent_id' => $catalogId, 'sort' => $sort, 'update_time' => $now]);
            $changed = $changed || $moved > 0;
        }

        // ---- 4. 样例数据:表为空时才写入,不覆盖用户自己的数据 ----
        $fill = static function (string $table, array $rows) use ($now, &$changed): void {
            if (Db::table($table)->whereNull('delete_time')->count() > 0) {
                return;
            }
            foreach ($rows as $row) {
                Db::table($table)->insert($row + ['create_time' => $now, 'update_time' => $now]);
            }
            $changed = true;
        };

        $fill('tl_demo_article', [
            ['title' => 'TLAdmin 快速上手:十分钟跑起第一个后台', 'author' => '秋风', 'category' => '入门', 'summary' => '从安装依赖、执行迁移到登录后台的完整步骤。', 'content' => "1. 安装依赖\n2. 配置 .env\n3. php bin/console migrate && php bin/console seed\n4. 启动前后端", 'views' => 1286, 'is_top' => 1, 'publish_time' => $now - 20 * $day, 'status' => 1],
            ['title' => '用代码生成器十秒生成一个 CRUD 模块', 'author' => '秋风', 'category' => '入门', 'summary' => '建表、选字段、预览、生成,菜单和权限一并写好。', 'content' => '本页就是代码生成器生成的。', 'views' => 932, 'is_top' => 1, 'publish_time' => $now - 15 * $day, 'status' => 1],
            ['title' => 'RBAC 权限设计:菜单、按钮、接口一套模型', 'author' => '林佳怡', 'category' => '进阶', 'summary' => '前端按钮权限与后端接口权限共用同一套标识。', 'content' => 'v-permission 指令 + 接口注解 permission。', 'views' => 657, 'is_top' => 0, 'publish_time' => $now - 12 * $day, 'status' => 1],
            ['title' => '数据权限:本部门、本人数据自动过滤', 'author' => '林佳怡', 'category' => '进阶', 'summary' => '角色配置 data_scope,列表查询自动加条件。', 'content' => '全部 / 本部门及以下 / 本部门 / 自定义 / 仅本人。', 'views' => 488, 'is_top' => 0, 'publish_time' => $now - 9 * $day, 'status' => 1],
            ['title' => '前端直传 OSS / COS / 七牛', 'author' => '陈晓峰', 'category' => '进阶', 'summary' => '后端签发凭证,文件不经过服务器带宽。', 'content' => 'UploadPlus 组件自动按当前存储磁盘选择上传方式。', 'views' => 403, 'is_top' => 0, 'publish_time' => $now - 6 * $day, 'status' => 1],
            ['title' => '部署上线清单:Nginx、定时任务、队列守护', 'author' => '陈晓峰', 'category' => '运维', 'summary' => '上线前逐项核对,避免漏配。', 'content' => '见 docs/tladmin-deploy.md。', 'views' => 215, 'is_top' => 0, 'publish_time' => $now - 3 * $day, 'status' => 1],
            ['title' => '下个版本计划(草稿)', 'author' => '秋风', 'category' => '公告', 'summary' => '草稿状态,前台不展示。', 'content' => '待定。', 'views' => 0, 'is_top' => 0, 'publish_time' => 0, 'status' => 0],
        ]);

        $fill('tl_demo_customer', [
            ['name' => '杭州云帆科技有限公司', 'contact' => '王经理', 'mobile' => '13800002001', 'source' => '官网', 'level' => 5, 'next_follow_time' => $now + 1 * $day, 'status' => 1, 'remark' => '年框客户'],
            ['name' => '深圳启明电子', 'contact' => '李总', 'mobile' => '13800002002', 'source' => '转介绍', 'level' => 4, 'next_follow_time' => $now + 2 * $day, 'status' => 1, 'remark' => ''],
            ['name' => '成都锦城餐饮管理', 'contact' => '赵女士', 'mobile' => '13800002003', 'source' => '展会', 'level' => 3, 'next_follow_time' => $now + 5 * $day, 'status' => 1, 'remark' => '关注小程序点餐'],
            ['name' => '北京知行教育', 'contact' => '孙老师', 'mobile' => '13800002004', 'source' => '官网', 'level' => 3, 'next_follow_time' => $now + 7 * $day, 'status' => 1, 'remark' => ''],
            ['name' => '苏州恒达物流', 'contact' => '周主管', 'mobile' => '13800002005', 'source' => '电话', 'level' => 2, 'next_follow_time' => $now + 10 * $day, 'status' => 1, 'remark' => ''],
            ['name' => '武汉青禾农业', 'contact' => '吴先生', 'mobile' => '13800002006', 'source' => '展会', 'level' => 2, 'next_follow_time' => $now + 14 * $day, 'status' => 1, 'remark' => ''],
            ['name' => '南京墨石设计工作室', 'contact' => '郑设计', 'mobile' => '13800002007', 'source' => '转介绍', 'level' => 1, 'next_follow_time' => 0, 'status' => 0, 'remark' => '已流失'],
        ]);

        $orderPrefix = 'DO' . date('Ymd', $now);
        $fill('tl_demo_order', [
            ['order_no' => $orderPrefix . '0001', 'customer_name' => '杭州云帆科技有限公司', 'goods_name' => '27 寸显示器', 'quantity' => 10, 'amount' => 18990, 'is_paid' => 1, 'pay_time' => $now - 5 * $day, 'remark' => ''],
            ['order_no' => $orderPrefix . '0002', 'customer_name' => '深圳启明电子', 'goods_name' => '机械键盘', 'quantity' => 30, 'amount' => 13770, 'is_paid' => 1, 'pay_time' => $now - 4 * $day, 'remark' => '开专票'],
            ['order_no' => $orderPrefix . '0003', 'customer_name' => '成都锦城餐饮管理', 'goods_name' => '人体工学椅', 'quantity' => 6, 'amount' => 7794, 'is_paid' => 1, 'pay_time' => $now - 2 * $day, 'remark' => ''],
            ['order_no' => $orderPrefix . '0004', 'customer_name' => '北京知行教育', 'goods_name' => '无线蓝牙耳机', 'quantity' => 50, 'amount' => 9950, 'is_paid' => 0, 'pay_time' => 0, 'remark' => '等对方走流程'],
            ['order_no' => $orderPrefix . '0005', 'customer_name' => '苏州恒达物流', 'goods_name' => 'USB-C 扩展坞', 'quantity' => 20, 'amount' => 4780, 'is_paid' => 0, 'pay_time' => 0, 'remark' => ''],
            ['order_no' => $orderPrefix . '0006', 'customer_name' => '武汉青禾农业', 'goods_name' => '移动电源', 'quantity' => 100, 'amount' => 12900, 'is_paid' => 1, 'pay_time' => $now - 1 * $day, 'remark' => ''],
        ]);

        $fill('tl_demo_notice', [
            ['title' => '系统将于本周六 02:00-04:00 停机维护', 'content' => '维护期间后台与接口暂停服务,请提前安排。', 'is_top' => 1, 'start_time' => $now - 1 * $day, 'end_time' => $now + 5 * $day, 'status' => 1],
            ['title' => '新增演示中心:文章、客户、订单、公告', 'content' => '这些页面都由代码生成器生成,可对照代码学习。', 'is_top' => 1, 'start_time' => $now - 2 * $day, 'end_time' => $now + 30 * $day, 'status' => 1],
            ['title' => '管理员请尽快绑定动态验证码', 'content' => '个人中心 → 动态验证码,使用 Google Authenticator 扫码绑定。', 'is_top' => 0, 'start_time' => $now - 7 * $day, 'end_time' => $now + 60 * $day, 'status' => 1],
            ['title' => '国庆假期值班安排', 'content' => '值班表见附件。', 'is_top' => 0, 'start_time' => $now + 3 * $day, 'end_time' => $now + 12 * $day, 'status' => 1],
            ['title' => '旧版接口下线通知(已过期)', 'content' => 'v0 接口已下线。', 'is_top' => 0, 'start_time' => $now - 40 * $day, 'end_time' => $now - 10 * $day, 'status' => 0],
        ]);

        return $changed;
    },
];
