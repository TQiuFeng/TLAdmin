<!--
  登录页:布局取自 TDesign 官方 starter(tdesign-vue-next-starter)登录页,
  接入 TLAdmin 登录逻辑(账号密码 + Google 动态验证码,后端返回 totp_required 时自动展开码输入框)。
  Author: qiufeng
-->
<template>
  <div class="login-wrapper">
    <header class="login-header">
      <div class="logo">TLAdmin</div>
    </header>

    <div class="login-container">
      <div class="title-container">
        <h1 class="title margin-no">登录到</h1>
        <h1 class="title">TLAdmin 管理后台</h1>
        <div class="sub-title">
          <p class="tip">中后台快速开发框架</p>
        </div>
      </div>

      <t-form
        ref="formRef"
        class="item-container"
        :data="form"
        :rules="rules"
        label-width="0"
        @submit="onSubmit"
      >
        <t-form-item name="username">
          <t-input v-model="form.username" size="large" placeholder="请输入账号">
            <template #prefix-icon><user-icon /></template>
          </t-input>
        </t-form-item>

        <t-form-item name="password">
          <t-input
            v-model="form.password"
            size="large"
            :type="showPsw ? 'text' : 'password'"
            clearable
            placeholder="请输入登录密码"
          >
            <template #prefix-icon><lock-on-icon /></template>
            <template #suffix-icon>
              <browse-icon v-if="showPsw" class="psw-toggle" @click="showPsw = !showPsw" />
              <browse-off-icon v-else class="psw-toggle" @click="showPsw = !showPsw" />
            </template>
          </t-input>
        </t-form-item>

        <t-form-item v-if="totpRequired" name="code">
          <t-input v-model="form.code" size="large" clearable placeholder="请输入 6 位动态验证码" :maxlength="6" autofocus>
            <template #prefix-icon><secured-icon /></template>
          </t-input>
        </t-form-item>

        <div class="check-container remember-pwd">
          <t-checkbox v-model="remember">记住账号</t-checkbox>
        </div>

        <t-form-item class="btn-container">
          <t-button block size="large" type="submit" :loading="loading">登录</t-button>
        </t-form-item>
      </t-form>
    </div>

    <footer class="copyright">Copyright &copy; {{ new Date().getFullYear() }} TLAdmin. All Rights Reserved</footer>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { MessagePlugin, type FormProps, type FormInstanceFunctions } from 'tdesign-vue-next';
import { UserIcon, LockOnIcon, SecuredIcon, BrowseIcon, BrowseOffIcon } from 'tdesign-icons-vue-next';
import { useUserStore } from '@/stores/user';
import type { ApiResult } from '@/types/api';

const REMEMBER_KEY = 'tladmin:remember_username';

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();

const formRef = ref<FormInstanceFunctions>();
const loading = ref(false);
const totpRequired = ref(false);
const showPsw = ref(false);
const remember = ref(false);

const form = reactive({ username: '', password: '', code: '' });

onMounted(() => {
  const saved = localStorage.getItem(REMEMBER_KEY);
  if (saved) {
    form.username = saved;
    remember.value = true;
  }
});

const rules: FormProps['rules'] = {
  username: [{ required: true, message: '请输入账号', trigger: 'blur' }],
  password: [{ required: true, message: '请输入登录密码', trigger: 'blur' }],
  code: [
    { required: true, message: '请输入动态验证码', trigger: 'blur' },
    { pattern: /^\d{6}$/, message: '动态验证码为 6 位数字', trigger: 'blur' },
  ],
};

const onSubmit: FormProps['onSubmit'] = async ({ validateResult }) => {
  if (validateResult !== true) return;
  loading.value = true;
  try {
    await userStore.login({
      username: form.username,
      password: form.password,
      ...(totpRequired.value ? { code: form.code } : {}),
    });
    if (remember.value) {
      localStorage.setItem(REMEMBER_KEY, form.username);
    } else {
      localStorage.removeItem(REMEMBER_KEY);
    }
    MessagePlugin.success('登录成功');
    const redirect = (route.query.redirect as string) || '/';
    await router.replace(redirect);
  } catch (err) {
    const result = err as ApiResult<{ totp_required?: boolean }>;
    // 后端提示需要动态码:展开动态码输入框,不重置已填账号密码
    if (result?.data?.totp_required) {
      totpRequired.value = true;
      form.code = '';
    }
    MessagePlugin.error(result?.message || '登录失败');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* 布局与官方 starter login/index.less 对齐(less 转 css) */
.login-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #fff;
  background-image: url('@/assets/assets-login-bg-white.png');
  background-size: cover;
  background-position: 100%;
}

.login-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: var(--td-comp-size-xxxl, 64px);
  padding: 0 var(--td-comp-paddingLR-xl, 24px);
  color: var(--td-text-color-primary);
  backdrop-filter: blur(10px);
}

.login-header .logo {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 1px;
  color: var(--td-brand-color, #0052d9);
}

.login-container {
  position: absolute;
  top: 22%;
  left: 5%;
  min-height: 500px;
}

.title-container .title {
  margin: 0;
  margin-top: var(--td-comp-margin-xs, 4px);
  font: var(--td-font-headline-large, 600 36px/44px sans-serif);
  color: var(--td-text-color-primary);
}

.title-container .title.margin-no {
  margin-top: 0;
}

.sub-title {
  margin-top: var(--td-comp-margin-xxl, 24px);
}

.sub-title .tip {
  display: inline-block;
  margin: 0;
  margin-right: var(--td-comp-margin-s, 8px);
  font: var(--td-font-body-medium, 400 14px/22px sans-serif);
  color: var(--td-text-color-secondary);
}

.item-container {
  width: 400px;
  margin-top: var(--td-comp-margin-xxxxl, 36px);
}

.check-container {
  display: flex;
  align-items: center;
  font: var(--td-font-body-medium, 400 14px/22px sans-serif);
  color: var(--td-text-color-secondary);
}

.check-container.remember-pwd {
  justify-content: space-between;
  margin-bottom: var(--td-comp-margin-l, 16px);
}

.btn-container {
  margin-top: var(--td-comp-margin-xxxxl, 36px);
}

.psw-toggle {
  cursor: pointer;
}

.copyright {
  position: absolute;
  bottom: 64px;
  left: 5%;
  font: var(--td-font-body-medium, 400 14px/22px sans-serif);
  color: var(--td-text-color-secondary);
}

@media screen and (height <= 700px) {
  .copyright {
    display: none;
  }
}
</style>
