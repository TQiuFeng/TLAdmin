<!--
  第三方插件配置:支付/微信/短信/存储。schema 驱动动态表单 + 依赖包状态 + 密钥脱敏保存。
  Author: qiufeng
-->
<template>
  <t-card :bordered="false" :loading="loading">
    <t-alert
      theme="info"
      class="tip"
      message="配置各第三方能力的密钥与渠道。密钥保存后以脱敏(******)展示,留空或不改则不覆盖原值;通过选择默认渠道/磁盘来决定启用哪个服务商。"
    />

    <t-tabs v-model="activeGroup">
      <t-tab-panel v-for="g in groups" :key="g.group" :value="g.group" :label="GROUP_LABELS[g.group] ?? g.group">
        <div class="panel">
          <!-- 依赖包状态 -->
          <div class="packages">
            <span class="pkg-label">依赖包:</span>
            <t-tag
              v-for="pkg in g.packages"
              :key="pkg.key"
              :theme="pkg.installed ? 'success' : 'warning'"
              variant="light"
            >
              {{ pkg.title }} · {{ pkg.name }} · {{ pkg.installed ? '已安装' : '未安装' }}
            </t-tag>
          </div>

          <!-- schema 驱动表单 -->
          <t-form label-width="160px" class="config-form">
            <t-form-item v-for="f in g.schema" :key="f.field" :label="f.label">
              <t-textarea
                v-if="f.type === 'textarea'"
                v-model="forms[g.group][f.field]"
                :placeholder="f.secret ? '不修改留空即可' : ''"
                :autosize="{ minRows: 2, maxRows: 6 }"
              />
              <t-select v-else-if="f.type === 'select'" v-model="forms[g.group][f.field]" clearable style="width: 280px">
                <t-option v-for="o in f.options ?? []" :key="o" :label="o" :value="o" />
              </t-select>
              <t-checkbox-group v-else-if="f.type === 'checkbox'" v-model="forms[g.group][f.field]">
                <t-checkbox v-for="o in f.options ?? []" :key="o" :value="o">{{ o }}</t-checkbox>
              </t-checkbox-group>
              <t-switch v-else-if="f.type === 'switch'" v-model="forms[g.group][f.field]" />
              <t-input
                v-else
                v-model="forms[g.group][f.field]"
                :type="f.type === 'password' ? 'password' : 'text'"
                :placeholder="f.secret ? '不修改留空即可' : ''"
                style="width: 280px"
              />
            </t-form-item>

            <t-form-item>
              <t-button
                v-permission="'system:config:update'"
                theme="primary"
                :loading="saving === g.group"
                @click="save(g)"
              >保存{{ GROUP_LABELS[g.group] ?? '' }}配置</t-button>
            </t-form-item>
          </t-form>
        </div>
      </t-tab-panel>
    </t-tabs>
  </t-card>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { MessagePlugin } from 'tdesign-vue-next';
import { get } from 'lodash-es';
import { getIntegrations, saveIntegration, type IntegrationGroup } from '@/api/system/integration';

const GROUP_LABELS: Record<string, string> = {
  pay: '支付',
  wechat: '微信公众号',
  sms: '短信',
  storage: '存储',
};

const loading = ref(false);
const saving = ref('');
const activeGroup = ref('pay');
const groups = ref<IntegrationGroup[]>([]);
/** 每个能力组的扁平表单值:{ group: { field: value } } */
const forms = reactive<Record<string, Record<string, unknown>>>({});

/** 按 schema 从嵌套 config 取出每个字段的当前值 */
function buildForm(g: IntegrationGroup): Record<string, unknown> {
  const config = g.config ?? {};
  const form: Record<string, unknown> = {};
  for (const f of g.schema) {
    if (f.type === 'checkbox') {
      form[f.field] = get(config, f.field, []) ?? [];
    } else if (f.type === 'switch') {
      form[f.field] = !!get(config, f.field, false);
    } else {
      form[f.field] = get(config, f.field, '') ?? '';
    }
  }
  return form;
}

async function load(): Promise<void> {
  loading.value = true;
  try {
    const data = await getIntegrations();
    groups.value = data.list;
    for (const g of data.list) {
      forms[g.group] = buildForm(g);
    }
    if (data.list[0]) activeGroup.value = data.list[0].group;
  } finally {
    loading.value = false;
  }
}

onMounted(load);

async function save(g: IntegrationGroup): Promise<void> {
  saving.value = g.group;
  try {
    const updated = await saveIntegration(g.group, forms[g.group]);
    // 用返回的脱敏配置刷新本组,保持与后端一致
    const idx = groups.value.findIndex((x) => x.group === g.group);
    if (idx >= 0) groups.value[idx] = updated;
    forms[g.group] = buildForm(updated);
    MessagePlugin.success('保存成功');
  } finally {
    saving.value = '';
  }
}
</script>

<style scoped>
.tip {
  margin-bottom: 16px;
}

.panel {
  padding-top: 12px;
}

.packages {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  align-items: center;
  margin-bottom: 20px;
}

.pkg-label {
  color: var(--td-text-color-secondary);
}

.config-form {
  max-width: 720px;
}
</style>
