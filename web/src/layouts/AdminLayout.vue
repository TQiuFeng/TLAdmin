<!--
  后台主布局:左侧动态菜单 + 顶栏(折叠/面包屑/用户菜单)+ 多标签页 + 内容区。
  Author: qiufeng
-->
<template>
  <t-layout class="admin-layout">
    <t-aside :width="collapsed ? '64px' : '232px'" class="layout-aside">
      <t-menu theme="dark" :value="activePath" :collapsed="collapsed" class="layout-menu">
        <template #logo>
          <div class="layout-logo" :class="{ collapsed }">
            <span v-if="!collapsed">TLAdmin</span>
            <span v-else>TL</span>
          </div>
        </template>
        <menu-tree :menus="userStore.menus" />
      </t-menu>
    </t-aside>

    <t-layout>
      <t-header class="layout-header">
        <div class="header-left">
          <t-button variant="text" shape="square" @click="collapsed = !collapsed">
            <view-list-icon />
          </t-button>
          <t-breadcrumb>
            <t-breadcrumb-item v-for="item in breadcrumbs" :key="item">{{ item }}</t-breadcrumb-item>
          </t-breadcrumb>
        </div>

        <div class="header-right">
          <t-button variant="text" shape="square" title="全屏" @click="toggleFullscreen">
            <fullscreen-icon />
          </t-button>
          <t-dropdown :options="userMenuOptions" @click="onUserMenuClick">
            <t-button variant="text" class="user-btn">
              <t-avatar size="28px">{{ avatarText }}</t-avatar>
              <span class="user-name">{{ userStore.user?.nickname || userStore.user?.username }}</span>
              <chevron-down-icon />
            </t-button>
          </t-dropdown>
        </div>
      </t-header>

      <div class="layout-tabs">
        <t-tabs :value="activePath" theme="card" @change="onTabChange" @remove="onTabRemove">
          <t-tab-panel
            v-for="tab in tabs"
            :key="tab.path"
            :value="tab.path"
            :label="tab.title"
            :removable="tabs.length > 1"
          />
        </t-tabs>
      </div>

      <t-content class="layout-content">
        <router-view v-slot="{ Component }">
          <component :is="Component" />
        </router-view>
      </t-content>
    </t-layout>
  </t-layout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { DialogPlugin, type DropdownProps } from 'tdesign-vue-next';
import { ViewListIcon, FullscreenIcon, ChevronDownIcon } from 'tdesign-icons-vue-next';
import { useUserStore } from '@/stores/user';
import type { MenuNode } from '@/types/auth';
import MenuTree from '@/layouts/components/MenuTree.vue';

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

const collapsed = ref(false);
const activePath = computed(() => route.path);

// ---- 面包屑:在菜单树中找到当前路径的祖先链 ----
const breadcrumbs = computed<string[]>(() => {
  const chain: string[] = [];
  const walk = (nodes: MenuNode[], trail: string[]): boolean => {
    for (const node of nodes) {
      const next = [...trail, node.title];
      if (node.path === route.path) {
        chain.push(...next);
        return true;
      }
      if (node.children?.length && walk(node.children, next)) return true;
    }
    return false;
  };
  walk(userStore.menus, []);
  return chain.length ? chain : [String(route.meta.title ?? '')];
});

// ---- 多标签页 ----
interface TabItem {
  path: string;
  title: string;
}

const tabs = ref<TabItem[]>([]);

watch(
  () => route.path,
  () => {
    if (route.meta.public) return;
    if (!tabs.value.some((t) => t.path === route.path)) {
      tabs.value.push({ path: route.path, title: String(route.meta.title ?? route.path) });
    }
  },
  { immediate: true },
);

function onTabChange(value: string | number): void {
  if (value !== route.path) router.push(String(value));
}

function onTabRemove(ctx: { value: string | number }): void {
  const path = String(ctx.value);
  const index = tabs.value.findIndex((t) => t.path === path);
  tabs.value.splice(index, 1);
  // 关掉当前页时跳到相邻标签
  if (path === route.path && tabs.value.length) {
    router.push(tabs.value[Math.min(index, tabs.value.length - 1)]!.path);
  }
}

// ---- 顶栏右侧 ----
const avatarText = computed(() => (userStore.user?.nickname || userStore.user?.username || '?').slice(0, 1));

const userMenuOptions: DropdownProps['options'] = [
  { content: '个人中心', value: 'profile' },
  { content: '退出登录', value: 'logout' },
];

async function onUserMenuClick(data: { value: unknown }): Promise<void> {
  if (data.value === 'profile') {
    router.push('/profile');
    return;
  }
  if (data.value === 'logout') {
    const dialog = DialogPlugin.confirm({
      header: '退出登录',
      body: '确定要退出当前账号吗?',
      onConfirm: async () => {
        await userStore.logout();
        dialog.destroy();
        router.replace('/login');
      },
    });
  }
}

function toggleFullscreen(): void {
  if (document.fullscreenElement) {
    document.exitFullscreen();
  } else {
    document.documentElement.requestFullscreen();
  }
}
</script>

<style scoped>
.admin-layout {
  height: 100vh;
}

.layout-aside {
  border-right: 1px solid var(--td-component-stroke);
  transition: width 0.2s;
}

.layout-menu {
  height: 100%;
}

.layout-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  font-size: 20px;
  font-weight: 600;
  color: #fff;
  letter-spacing: 1px;
}

.layout-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  background: var(--td-bg-color-container);
  border-bottom: 1px solid var(--td-component-stroke);
}

.header-left,
.header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 8px;
}

.user-name {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.layout-tabs {
  background: var(--td-bg-color-container);
  border-bottom: 1px solid var(--td-component-stroke);
}

.layout-content {
  padding: 16px;
  overflow: auto;
  background: var(--td-bg-color-page);
}
</style>
