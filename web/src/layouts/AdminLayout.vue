<!--
  后台主布局:左侧动态菜单 + 顶栏(折叠/面包屑/用户菜单)+ 多标签页 + 内容区。
  Author: qiufeng
-->
<template>
  <div class="admin-layout">
    <aside class="layout-aside" :class="{ collapsed }">
      <router-link to="/" class="layout-brand">
        <img :src="logoUrl" alt="TLAdmin" />
        <span v-show="!collapsed" class="layout-brand__name">TLAdmin<em>管理后台</em></span>
      </router-link>
      <t-menu
        theme="light"
        v-model:expanded="expanded"
        :value="activePath"
        :collapsed="collapsed"
        :width="['220px', '64px']"
        expand-mutex
        class="layout-menu"
      >
        <menu-tree :menus="userStore.menus" />
      </t-menu>
    </aside>

    <div class="layout-main">
      <header class="layout-header">
        <div class="header-left">
          <t-tooltip :content="collapsed ? '展开菜单' : '收起菜单'" placement="bottom">
            <button class="header-icon-btn" type="button" @click="collapsed = !collapsed">
              <menu-unfold-icon v-if="collapsed" size="18px" />
              <menu-fold-icon v-else size="18px" />
            </button>
          </t-tooltip>
          <t-breadcrumb>
            <t-breadcrumb-item v-for="item in breadcrumbs" :key="item">{{ item }}</t-breadcrumb-item>
          </t-breadcrumb>
        </div>

        <div class="header-right">
          <t-tooltip :content="isFullscreen ? '退出全屏' : '全屏'" placement="bottom">
            <button class="header-icon-btn" type="button" @click="toggleFullscreen">
              <fullscreen-exit-icon v-if="isFullscreen" size="18px" />
              <fullscreen-icon v-else size="18px" />
            </button>
          </t-tooltip>
          <t-dropdown :options="userMenuOptions" trigger="click" @click="onUserMenuClick">
            <div class="header-user">
              <t-avatar size="30px" :image="userStore.user?.avatar || undefined">{{ avatarText }}</t-avatar>
              <div class="header-user__meta">
                <span class="header-user__name">{{ userStore.user?.nickname || userStore.user?.username }}</span>
                <span v-if="roleText" class="header-user__role">{{ roleText }}</span>
              </div>
              <chevron-down-icon class="header-user__caret" />
            </div>
          </t-dropdown>
        </div>
      </header>

      <nav class="layout-tabs">
        <div
          v-for="tab in tabs"
          :key="tab.path"
          class="layout-tab"
          :class="{ active: tab.path === activePath }"
          @click="onTabChange(tab.path)"
        >
          <span>{{ tab.title }}</span>
          <close-icon v-if="tabs.length > 1" class="layout-tab__close" @click.stop="onTabRemove(tab.path)" />
        </div>
      </nav>

      <main class="layout-content">
        <router-view v-slot="{ Component }">
          <!-- 页面多为多根节点(表格 + 弹窗),包一层 div,否则 Transition 无法执行离场动画,切换后内容区空白 -->
          <transition name="fade-slide" mode="out-in">
            <div :key="route.path">
              <component :is="Component" />
            </div>
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { DialogPlugin, type DropdownProps } from 'tdesign-vue-next';
import {
  MenuFoldIcon,
  MenuUnfoldIcon,
  FullscreenIcon,
  FullscreenExitIcon,
  ChevronDownIcon,
  CloseIcon,
} from 'tdesign-icons-vue-next';
import { useUserStore } from '@/stores/user';
import type { MenuNode } from '@/types/auth';
import MenuTree from '@/layouts/components/MenuTree.vue';
import logoUrl from '@/assets/logo.svg';

const COLLAPSE_KEY = 'tladmin:menu_collapsed';

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

// ---- 菜单折叠:记住上次状态,存储不可用时按展开处理 ----
function readCollapsed(): boolean {
  try {
    return localStorage.getItem(COLLAPSE_KEY) === '1';
  } catch {
    return false;
  }
}

const collapsed = ref(readCollapsed());
watch(collapsed, (value) => {
  try {
    localStorage.setItem(COLLAPSE_KEY, value ? '1' : '0');
  } catch {
    /* 隐私模式等场景写不进去,忽略 */
  }
});

const activePath = computed(() => route.path);

// ---- 展开当前页所在的目录(直接打开深层地址或刷新时,侧栏也能定位到当前菜单) ----
const expanded = ref<Array<string | number>>([]);

function findAncestors(nodes: MenuNode[], trail: string[]): string[] | null {
  for (const node of nodes) {
    if (node.path === route.path) return trail;
    if (node.children?.length) {
      // 与 MenuTree 中 t-submenu 的 value 保持一致
      const found = findAncestors(node.children, [...trail, node.path || String(node.id)]);
      if (found) return found;
    }
  }
  return null;
}

watch(
  [() => route.path, () => userStore.menus],
  () => {
    const ancestors = findAncestors(userStore.menus, []);
    if (ancestors?.length) expanded.value = ancestors;
  },
  { immediate: true },
);

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

function onTabChange(path: string): void {
  if (path !== route.path) router.push(path);
}

function onTabRemove(path: string): void {
  const index = tabs.value.findIndex((t) => t.path === path);
  tabs.value.splice(index, 1);
  // 关掉当前页时跳到相邻标签
  if (path === route.path && tabs.value.length) {
    router.push(tabs.value[Math.min(index, tabs.value.length - 1)]!.path);
  }
}

// ---- 顶栏右侧 ----
const avatarText = computed(() => (userStore.user?.nickname || userStore.user?.username || '?').slice(0, 1));

const roleText = computed(() =>
  userStore.user?.is_super === 1 ? '超级管理员' : userStore.roles.map((r) => r.name).join(' / '),
);

const userMenuOptions: DropdownProps['options'] = [
  { content: '个人中心', value: 'profile' },
  { content: '退出登录', value: 'logout', divider: true },
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

const isFullscreen = ref(false);

function syncFullscreen(): void {
  isFullscreen.value = Boolean(document.fullscreenElement);
}

function toggleFullscreen(): void {
  if (document.fullscreenElement) {
    document.exitFullscreen();
  } else {
    document.documentElement.requestFullscreen();
  }
}

onMounted(() => document.addEventListener('fullscreenchange', syncFullscreen));
onBeforeUnmount(() => document.removeEventListener('fullscreenchange', syncFullscreen));
</script>

<style scoped>
.admin-layout {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

/* ---------- 侧栏 ---------- */
.layout-aside {
  display: flex;
  flex: 0 0 auto;
  flex-direction: column;
  width: 220px;
  background: #fff;
  border-right: 1px solid var(--tl-line);
  transition: width 0.2s ease;
}

.layout-aside.collapsed {
  width: 64px;
}

.layout-brand {
  display: flex;
  flex: 0 0 auto;
  align-items: center;
  gap: 10px;
  height: 56px;
  padding: 0 17px;
  overflow: hidden;
  white-space: nowrap;
  color: var(--tl-text-1);
  border-bottom: 1px solid var(--tl-line);
}

.layout-brand img {
  flex: 0 0 auto;
  width: 30px;
  height: 30px;
}

.layout-brand__name {
  font-size: 16px;
  font-weight: 700;
  line-height: 1.2;
  letter-spacing: 0.5px;
}

.layout-brand__name em {
  display: block;
  font-size: 11px;
  font-style: normal;
  font-weight: 400;
  letter-spacing: 1px;
  color: var(--tl-text-3);
}

.layout-menu {
  flex: 1;
  min-height: 0;
}

.layout-menu :deep(.t-menu__item) {
  height: 42px;
  color: #4b5563;
}

.layout-menu :deep(.t-menu__item.t-is-active:not(.t-is-opened)) {
  font-weight: 500;
  color: var(--tl-primary);
  background: var(--tl-primary-light);
}

.layout-menu :deep(.t-menu__item:hover:not(.t-is-active):not(.t-is-disabled)) {
  color: var(--tl-text-1);
  background: var(--tl-hover);
}

.layout-menu :deep(.t-submenu.t-is-active > .t-menu__item) {
  color: var(--tl-primary);
}

/* ---------- 主区域 ---------- */
.layout-main {
  display: flex;
  flex: 1;
  flex-direction: column;
  min-width: 0;
}

.layout-header {
  display: flex;
  flex: 0 0 auto;
  align-items: center;
  justify-content: space-between;
  height: 56px;
  padding: 0 20px 0 12px;
  background: #fff;
  border-bottom: 1px solid var(--tl-line);
}

.header-left,
.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.header-left {
  gap: 10px;
}

.header-icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 34px;
  height: 34px;
  padding: 0;
  color: var(--tl-text-2);
  cursor: pointer;
  background: transparent;
  border: none;
  border-radius: 6px;
}

.header-icon-btn:hover {
  color: var(--tl-text-1);
  background: var(--tl-hover);
}

.header-user {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 4px 8px;
  cursor: pointer;
  border-radius: 8px;
}

.header-user:hover {
  background: var(--tl-hover);
}

.header-user :deep(.t-avatar) {
  font-weight: 600;
  color: #fff;
  background: var(--tl-primary);
}

.header-user__meta {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.header-user__name,
.header-user__role {
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.header-user__name {
  font-size: 14px;
  color: var(--tl-text-1);
}

.header-user__role {
  font-size: 11px;
  color: var(--tl-text-3);
}

.header-user__caret {
  color: var(--tl-text-3);
}

/* ---------- 标签栏 ---------- */
.layout-tabs {
  display: flex;
  flex: 0 0 auto;
  align-items: center;
  gap: 6px;
  height: 40px;
  padding: 0 12px;
  overflow-x: auto;
  background: #fff;
  border-bottom: 1px solid var(--tl-line);
}

.layout-tab {
  display: inline-flex;
  flex: 0 0 auto;
  align-items: center;
  gap: 4px;
  height: 28px;
  padding: 0 10px;
  font-size: 13px;
  color: var(--tl-text-2);
  cursor: pointer;
  border-radius: 6px;
  transition: background 0.15s, color 0.15s;
}

.layout-tab:hover {
  color: var(--tl-text-1);
  background: var(--tl-hover);
}

.layout-tab.active {
  font-weight: 500;
  color: var(--tl-primary);
  background: var(--tl-primary-light);
}

.layout-tab__close {
  font-size: 14px;
  color: var(--tl-text-3);
  border-radius: 50%;
}

.layout-tab__close:hover {
  color: #fff;
  background: var(--tl-text-3);
}

.layout-content {
  flex: 1;
  min-height: 0;
  padding: 20px;
  overflow: auto;
}
</style>
