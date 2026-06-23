/**
 * 用户登录态 store:token、用户信息、菜单树、权限集合。
 * Author: qiufeng
 */
import { defineStore } from 'pinia';
import { login, logout, getProfile, type LoginParams } from '@/api/auth';
import { getDatetimeFormat } from '@/api/system/config';
import type { MenuNode, UserInfo, RoleInfo } from '@/types/auth';
import { getToken, setToken, clearToken } from '@/utils/token';
import { setGlobalDateFormat } from '@/utils/date';

interface UserState {
  token: string;
  user: UserInfo | null;
  roles: RoleInfo[];
  permissions: string[];
  menus: MenuNode[];
  /** profile 是否已加载(路由守卫用来决定是否需要拉取) */
  loaded: boolean;
}

export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    token: getToken(),
    user: null,
    roles: [],
    permissions: [],
    menus: [],
    loaded: false,
  }),

  getters: {
    /** 超管或拥有 * 权限时放行一切 */
    isSuper(state): boolean {
      return state.user?.is_super === 1 || state.permissions.includes('*');
    },
  },

  actions: {
    async login(params: LoginParams): Promise<void> {
      const pair = await login(params);
      setToken(pair.access_token, pair.refresh_token);
      this.token = pair.access_token;
    },

    async fetchProfile(): Promise<void> {
      const profile = await getProfile();
      this.user = profile.user;
      this.roles = profile.roles;
      this.permissions = profile.permissions;
      this.menus = profile.menus;
      this.loaded = true;

      // 拉取全局时间显示格式,后续所有 formatDate 默认按它渲染
      getDatetimeFormat()
        .then((cfg) => setGlobalDateFormat(cfg.value))
        .catch(() => {});
    },

    /** 是否拥有某权限标识(按钮/接口级控制) */
    hasPermission(permission: string): boolean {
      if (!permission) return true;
      return this.isSuper || this.permissions.includes(permission);
    },

    async logout(): Promise<void> {
      try {
        await logout();
      } catch {
        /* token 已失效也要继续清理本地状态 */
      }
      clearToken();
      this.$reset();
    },
  },
});
