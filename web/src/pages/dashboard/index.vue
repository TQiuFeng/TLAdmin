<!--
  仪表盘:登录后首页,展示当前账号概览。
  Author: qiufeng
-->
<template>
  <div class="dashboard">
    <t-card :bordered="false">
      <div class="welcome">
        <t-avatar size="56px">{{ avatarText }}</t-avatar>
        <div>
          <h2>{{ greeting }},{{ userStore.user?.nickname || userStore.user?.username }}</h2>
          <p>
            上次登录:{{ formatDate(userStore.user?.last_login_time) }}
            <template v-if="userStore.user?.last_login_ip">({{ userStore.user.last_login_ip }})</template>
          </p>
        </div>
      </div>
    </t-card>

    <div class="stat-row">
      <t-card title="角色" :bordered="false">
        <t-space break-line>
          <t-tag v-for="role in userStore.roles" :key="role.id" theme="primary" variant="light">
            {{ role.name }}
          </t-tag>
        </t-space>
      </t-card>
      <t-card title="权限点" :bordered="false">
        <span class="stat-num">{{ userStore.isSuper ? '全部' : userStore.permissions.length }}</span>
      </t-card>
      <t-card title="可见菜单" :bordered="false">
        <span class="stat-num">{{ menuCount }}</span>
      </t-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useUserStore } from '@/stores/user';
import { formatDate } from '@/utils/date';
import { treeToList } from '@/utils/array';

const userStore = useUserStore();

const avatarText = computed(() => (userStore.user?.nickname || userStore.user?.username || '?').slice(0, 1));

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 6) return '夜深了';
  if (hour < 12) return '早上好';
  if (hour < 14) return '中午好';
  if (hour < 18) return '下午好';
  return '晚上好';
});

const menuCount = computed(
  () => treeToList(userStore.menus).filter((m) => m.type === 'menu').length,
);
</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.welcome {
  display: flex;
  align-items: center;
  gap: 16px;
}

.welcome h2 {
  margin: 0 0 4px;
}

.welcome p {
  margin: 0;
  color: var(--td-text-color-secondary);
}

.stat-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.stat-num {
  font-size: 28px;
  font-weight: 600;
}
</style>
