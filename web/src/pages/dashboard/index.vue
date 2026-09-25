<!--
  仪表盘:登录后首页,展示当前账号概览与常用入口。
  Author: qiufeng
-->
<template>
  <div class="dashboard">
    <div class="dashboard__head">
      <div>
        <h2>{{ greeting }},{{ userStore.user?.nickname || userStore.user?.username }}</h2>
        <p>{{ today }}</p>
      </div>
    </div>

    <div class="stat-row">
      <div v-for="s in stats" :key="s.key" class="stat-card">
        <span class="stat-card__icon" :class="`is-${s.tint}`"><component :is="s.icon" size="22px" /></span>
        <div class="stat-card__meta">
          <span class="stat-card__label">{{ s.label }}</span>
          <span class="stat-card__value" :class="{ 'is-text': s.text }">{{ s.value }}</span>
        </div>
      </div>
    </div>

    <div class="dashboard__grid">
      <div class="panel">
        <div class="panel__header">
          <span class="panel__title">常用功能<small>来自当前账号可见菜单</small></span>
        </div>
        <div v-if="shortcuts.length" class="shortcut-grid">
          <router-link v-for="m in shortcuts" :key="m.id" :to="m.path" class="shortcut">
            <span class="shortcut__icon"><icon :name="m.icon || 'app'" size="20px" /></span>
            <span class="shortcut__label">{{ m.title }}</span>
          </router-link>
        </div>
        <p v-else class="empty-tip">当前账号还没有可见菜单</p>
      </div>

      <div class="panel">
        <div class="panel__header">
          <span class="panel__title">账号信息</span>
          <t-link theme="primary" hover="color" @click="router.push('/profile')">个人中心</t-link>
        </div>
        <div class="account">
          <t-avatar size="48px" :image="userStore.user?.avatar || undefined">{{ avatarText }}</t-avatar>
          <div>
            <div class="account__name">{{ userStore.user?.nickname || userStore.user?.username }}</div>
            <div class="account__sub">账号:{{ userStore.user?.username }}</div>
          </div>
        </div>
        <dl class="account__list">
          <dt>角色</dt>
          <dd>
            <t-space size="6px" break-line>
              <t-tag v-if="userStore.user?.is_super === 1" theme="danger" variant="light" size="small">超管</t-tag>
              <t-tag v-for="role in userStore.roles" :key="role.id" theme="primary" variant="light" size="small">
                {{ role.name }}
              </t-tag>
              <span v-if="!userStore.roles.length && userStore.user?.is_super !== 1">-</span>
            </t-space>
          </dd>
          <dt>上次登录</dt>
          <dd>{{ formatDate(userStore.user?.last_login_time) }}</dd>
          <dt>登录 IP</dt>
          <dd>{{ userStore.user?.last_login_ip || '-' }}</dd>
        </dl>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { Icon, UserSafetyIcon, KeyIcon, ViewModuleIcon, TimeIcon } from 'tdesign-icons-vue-next';
import dayjs from 'dayjs';
import { useUserStore } from '@/stores/user';
import { formatDate } from '@/utils/date';
import { treeToList } from '@/utils/array';

const router = useRouter();
const userStore = useUserStore();

const WEEKDAYS = ['日', '一', '二', '三', '四', '五', '六'];
const today = computed(() => {
  const now = dayjs();
  return `${now.format('YYYY年M月D日')} 星期${WEEKDAYS[now.day()]}`;
});

const avatarText = computed(() => (userStore.user?.nickname || userStore.user?.username || '?').slice(0, 1));

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 6) return '夜深了';
  if (hour < 12) return '早上好';
  if (hour < 14) return '中午好';
  if (hour < 18) return '下午好';
  return '晚上好';
});

const menuItems = computed(() =>
  treeToList(userStore.menus).filter((m) => m.type === 'menu' && m.visible === 1 && m.path),
);

const stats = computed(() => [
  {
    key: 'roles',
    label: '所属角色',
    value: userStore.user?.is_super === 1 ? '超管' : userStore.roles.length,
    icon: UserSafetyIcon,
    tint: 'green',
    text: userStore.user?.is_super === 1,
  },
  {
    key: 'perms',
    label: '权限点',
    value: userStore.isSuper ? '全部' : userStore.permissions.length,
    icon: KeyIcon,
    tint: 'blue',
    text: userStore.isSuper,
  },
  { key: 'menus', label: '可见菜单', value: menuItems.value.length, icon: ViewModuleIcon, tint: 'orange', text: false },
  {
    key: 'login',
    label: '上次登录',
    value: formatDate(userStore.user?.last_login_time, 'MM-DD HH:mm'),
    icon: TimeIcon,
    tint: 'purple',
    text: true,
  },
]);

/** 常用功能:取可见菜单(排除首页本身)前 8 个 */
const shortcuts = computed(() => menuItems.value.filter((m) => m.path !== '/' && m.path !== '/dashboard').slice(0, 8));
</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.dashboard__head h2 {
  margin: 0 0 4px;
  font-size: 20px;
  font-weight: 600;
  color: var(--tl-text-1);
}

.dashboard__head p {
  margin: 0;
  font-size: 12px;
  color: var(--tl-text-3);
}

/* ---------- 统计卡片 ---------- */
.stat-row {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: var(--tl-shadow);
}

.stat-card__icon {
  display: inline-flex;
  flex: 0 0 auto;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 12px;
}

.stat-card__icon.is-green {
  color: #16a37a;
  background: #e6f6f0;
}

.stat-card__icon.is-blue {
  color: #3b82f6;
  background: #eaf2fe;
}

.stat-card__icon.is-orange {
  color: #f59e0b;
  background: #fef4e2;
}

.stat-card__icon.is-purple {
  color: #8b5cf6;
  background: #f1ecfe;
}

.stat-card__meta {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.stat-card__label {
  font-size: 13px;
  color: var(--tl-text-2);
}

.stat-card__value {
  margin-top: 2px;
  font-size: 26px;
  font-weight: 600;
  line-height: 1.2;
  color: var(--tl-text-1);
}

.stat-card__value.is-text {
  font-size: 20px;
}

/* ---------- 常用功能 + 账号信息 ---------- */
.dashboard__grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
  gap: 16px;
}

.shortcut-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 12px;
}

.shortcut {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  color: var(--tl-text-1);
  border: 1px solid var(--tl-line);
  border-radius: 8px;
  transition: border-color 0.15s, background 0.15s;
}

.shortcut:hover {
  background: #f7fbf9;
  border-color: var(--td-brand-color-3);
}

.shortcut__icon {
  display: inline-flex;
  flex: 0 0 auto;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  color: var(--tl-primary);
  background: var(--tl-primary-light);
  border-radius: 8px;
}

.shortcut__label {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.empty-tip {
  margin: 24px 0;
  text-align: center;
  color: var(--tl-text-3);
}

.account {
  display: flex;
  align-items: center;
  gap: 14px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--tl-line);
}

.account :deep(.t-avatar) {
  font-weight: 600;
  color: #fff;
  background: var(--tl-primary);
}

.account__name {
  font-size: 16px;
  font-weight: 600;
  color: var(--tl-text-1);
}

.account__sub {
  margin-top: 2px;
  font-size: 12px;
  color: var(--tl-text-3);
}

.account__list {
  display: grid;
  grid-template-columns: 72px 1fr;
  row-gap: 12px;
  margin: 16px 0 0;
  font-size: 13px;
}

.account__list dt {
  color: var(--tl-text-2);
}

.account__list dd {
  margin: 0;
  color: var(--tl-text-1);
}

@media (max-width: 1200px) {
  .stat-row {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .dashboard__grid {
    grid-template-columns: minmax(0, 1fr);
  }

  .shortcut-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
