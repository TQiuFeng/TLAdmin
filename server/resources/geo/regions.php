<?php

/**
 * 归属地选项标准数据:国家 → 省 → 市 → 区县。
 *
 * 来源:民政部行政区划(china-division),省市做简称规范化以匹配 IP 库返回的归属地;
 * 区县为完整名(IP 库通常解析不到区县级,作选项展示用)。乡镇/村级因 IP 库无法识别
 * 且数据量过大(乡镇约4万、村约66万),不纳入屏蔽下拉。
 * 供 IpLocationService::regions() 使用。
 * Author: qiufeng
 */

return array (
  '中国' => 
  array (
    '北京' => 
    array (
      '东城区' => 
      array (
      ),
      '西城区' => 
      array (
      ),
      '朝阳区' => 
      array (
      ),
      '丰台区' => 
      array (
      ),
      '石景山区' => 
      array (
      ),
      '海淀区' => 
      array (
      ),
      '门头沟区' => 
      array (
      ),
      '房山区' => 
      array (
      ),
      '通州区' => 
      array (
      ),
      '顺义区' => 
      array (
      ),
      '昌平区' => 
      array (
      ),
      '大兴区' => 
      array (
      ),
      '怀柔区' => 
      array (
      ),
      '平谷区' => 
      array (
      ),
      '密云区' => 
      array (
      ),
      '延庆区' => 
      array (
      ),
    ),
    '天津' => 
    array (
      '和平区' => 
      array (
      ),
      '河东区' => 
      array (
      ),
      '河西区' => 
      array (
      ),
      '南开区' => 
      array (
      ),
      '河北区' => 
      array (
      ),
      '红桥区' => 
      array (
      ),
      '东丽区' => 
      array (
      ),
      '西青区' => 
      array (
      ),
      '津南区' => 
      array (
      ),
      '北辰区' => 
      array (
      ),
      '武清区' => 
      array (
      ),
      '宝坻区' => 
      array (
      ),
      '滨海新区' => 
      array (
      ),
      '宁河区' => 
      array (
      ),
      '静海区' => 
      array (
      ),
      '蓟州区' => 
      array (
      ),
    ),
    '河北' => 
    array (
      '石家庄' => 
      array (
        '长安区' => 
        array (
        ),
        '桥西区' => 
        array (
        ),
        '新华区' => 
        array (
        ),
        '井陉矿区' => 
        array (
        ),
        '裕华区' => 
        array (
        ),
        '藁城区' => 
        array (
        ),
        '鹿泉区' => 
        array (
        ),
        '栾城区' => 
        array (
        ),
        '井陉县' => 
        array (
        ),
        '正定县' => 
        array (
        ),
        '行唐县' => 
        array (
        ),
        '灵寿县' => 
        array (
        ),
        '高邑县' => 
        array (
        ),
        '深泽县' => 
        array (
        ),
        '赞皇县' => 
        array (
        ),
        '无极县' => 
        array (
        ),
        '平山县' => 
        array (
        ),
        '元氏县' => 
        array (
        ),
        '赵县' => 
        array (
        ),
        '石家庄高新技术产业开发区' => 
        array (
        ),
        '石家庄循环化工园区' => 
        array (
        ),
        '辛集市' => 
        array (
        ),
        '晋州市' => 
        array (
        ),
        '新乐市' => 
        array (
        ),
      ),
      '唐山' => 
      array (
        '路南区' => 
        array (
        ),
        '路北区' => 
        array (
        ),
        '古冶区' => 
        array (
        ),
        '开平区' => 
        array (
        ),
        '丰南区' => 
        array (
        ),
        '丰润区' => 
        array (
        ),
        '曹妃甸区' => 
        array (
        ),
        '滦南县' => 
        array (
        ),
        '乐亭县' => 
        array (
        ),
        '迁西县' => 
        array (
        ),
        '玉田县' => 
        array (
        ),
        '河北唐山芦台经济开发区' => 
        array (
        ),
        '唐山市汉沽管理区' => 
        array (
        ),
        '唐山高新技术产业开发区' => 
        array (
        ),
        '河北唐山海港经济开发区' => 
        array (
        ),
        '遵化市' => 
        array (
        ),
        '迁安市' => 
        array (
        ),
        '滦州市' => 
        array (
        ),
      ),
      '秦皇岛' => 
      array (
        '海港区' => 
        array (
        ),
        '山海关区' => 
        array (
        ),
        '北戴河区' => 
        array (
        ),
        '抚宁区' => 
        array (
        ),
        '青龙满族自治县' => 
        array (
        ),
        '昌黎县' => 
        array (
        ),
        '卢龙县' => 
        array (
        ),
        '秦皇岛市经济技术开发区' => 
        array (
        ),
        '北戴河新区' => 
        array (
        ),
      ),
      '邯郸' => 
      array (
        '邯山区' => 
        array (
        ),
        '丛台区' => 
        array (
        ),
        '复兴区' => 
        array (
        ),
        '峰峰矿区' => 
        array (
        ),
        '肥乡区' => 
        array (
        ),
        '永年区' => 
        array (
        ),
        '临漳县' => 
        array (
        ),
        '成安县' => 
        array (
        ),
        '大名县' => 
        array (
        ),
        '涉县' => 
        array (
        ),
        '磁县' => 
        array (
        ),
        '邱县' => 
        array (
        ),
        '鸡泽县' => 
        array (
        ),
        '广平县' => 
        array (
        ),
        '馆陶县' => 
        array (
        ),
        '魏县' => 
        array (
        ),
        '曲周县' => 
        array (
        ),
        '邯郸经济技术开发区' => 
        array (
        ),
        '邯郸冀南新区' => 
        array (
        ),
        '武安市' => 
        array (
        ),
      ),
      '邢台' => 
      array (
        '襄都区' => 
        array (
        ),
        '信都区' => 
        array (
        ),
        '任泽区' => 
        array (
        ),
        '南和区' => 
        array (
        ),
        '临城县' => 
        array (
        ),
        '内丘县' => 
        array (
        ),
        '柏乡县' => 
        array (
        ),
        '隆尧县' => 
        array (
        ),
        '宁晋县' => 
        array (
        ),
        '巨鹿县' => 
        array (
        ),
        '新河县' => 
        array (
        ),
        '广宗县' => 
        array (
        ),
        '平乡县' => 
        array (
        ),
        '威县' => 
        array (
        ),
        '清河县' => 
        array (
        ),
        '临西县' => 
        array (
        ),
        '河北邢台经济开发区' => 
        array (
        ),
        '南宫市' => 
        array (
        ),
        '沙河市' => 
        array (
        ),
      ),
      '保定' => 
      array (
        '竞秀区' => 
        array (
        ),
        '莲池区' => 
        array (
        ),
        '满城区' => 
        array (
        ),
        '清苑区' => 
        array (
        ),
        '徐水区' => 
        array (
        ),
        '涞水县' => 
        array (
        ),
        '阜平县' => 
        array (
        ),
        '定兴县' => 
        array (
        ),
        '唐县' => 
        array (
        ),
        '高阳县' => 
        array (
        ),
        '容城县' => 
        array (
        ),
        '涞源县' => 
        array (
        ),
        '望都县' => 
        array (
        ),
        '安新县' => 
        array (
        ),
        '易县' => 
        array (
        ),
        '曲阳县' => 
        array (
        ),
        '蠡县' => 
        array (
        ),
        '顺平县' => 
        array (
        ),
        '博野县' => 
        array (
        ),
        '雄县' => 
        array (
        ),
        '保定高新技术产业开发区' => 
        array (
        ),
        '保定白沟新城' => 
        array (
        ),
        '涿州市' => 
        array (
        ),
        '定州市' => 
        array (
        ),
        '安国市' => 
        array (
        ),
        '高碑店市' => 
        array (
        ),
      ),
      '张家口' => 
      array (
        '桥东区' => 
        array (
        ),
        '桥西区' => 
        array (
        ),
        '宣化区' => 
        array (
        ),
        '下花园区' => 
        array (
        ),
        '万全区' => 
        array (
        ),
        '崇礼区' => 
        array (
        ),
        '张北县' => 
        array (
        ),
        '康保县' => 
        array (
        ),
        '沽源县' => 
        array (
        ),
        '尚义县' => 
        array (
        ),
        '蔚县' => 
        array (
        ),
        '阳原县' => 
        array (
        ),
        '怀安县' => 
        array (
        ),
        '怀来县' => 
        array (
        ),
        '涿鹿县' => 
        array (
        ),
        '赤城县' => 
        array (
        ),
        '张家口经济开发区' => 
        array (
        ),
        '张家口市察北管理区' => 
        array (
        ),
        '张家口市塞北管理区' => 
        array (
        ),
      ),
      '承德' => 
      array (
        '双桥区' => 
        array (
        ),
        '双滦区' => 
        array (
        ),
        '鹰手营子矿区' => 
        array (
        ),
        '承德县' => 
        array (
        ),
        '兴隆县' => 
        array (
        ),
        '滦平县' => 
        array (
        ),
        '隆化县' => 
        array (
        ),
        '丰宁满族自治县' => 
        array (
        ),
        '宽城满族自治县' => 
        array (
        ),
        '围场满族蒙古族自治县' => 
        array (
        ),
        '承德高新技术产业开发区' => 
        array (
        ),
        '平泉市' => 
        array (
        ),
      ),
      '沧州' => 
      array (
        '新华区' => 
        array (
        ),
        '运河区' => 
        array (
        ),
        '沧县' => 
        array (
        ),
        '青县' => 
        array (
        ),
        '东光县' => 
        array (
        ),
        '海兴县' => 
        array (
        ),
        '盐山县' => 
        array (
        ),
        '肃宁县' => 
        array (
        ),
        '南皮县' => 
        array (
        ),
        '吴桥县' => 
        array (
        ),
        '献县' => 
        array (
        ),
        '孟村回族自治县' => 
        array (
        ),
        '河北沧州经济开发区' => 
        array (
        ),
        '沧州高新技术产业开发区' => 
        array (
        ),
        '沧州渤海新区' => 
        array (
        ),
        '泊头市' => 
        array (
        ),
        '任丘市' => 
        array (
        ),
        '黄骅市' => 
        array (
        ),
        '河间市' => 
        array (
        ),
      ),
      '廊坊' => 
      array (
        '安次区' => 
        array (
        ),
        '广阳区' => 
        array (
        ),
        '固安县' => 
        array (
        ),
        '永清县' => 
        array (
        ),
        '香河县' => 
        array (
        ),
        '大城县' => 
        array (
        ),
        '文安县' => 
        array (
        ),
        '大厂回族自治县' => 
        array (
        ),
        '廊坊经济技术开发区' => 
        array (
        ),
        '霸州市' => 
        array (
        ),
        '三河市' => 
        array (
        ),
      ),
      '衡水' => 
      array (
        '桃城区' => 
        array (
        ),
        '冀州区' => 
        array (
        ),
        '枣强县' => 
        array (
        ),
        '武邑县' => 
        array (
        ),
        '武强县' => 
        array (
        ),
        '饶阳县' => 
        array (
        ),
        '安平县' => 
        array (
        ),
        '故城县' => 
        array (
        ),
        '景县' => 
        array (
        ),
        '阜城县' => 
        array (
        ),
        '河北衡水高新技术产业开发区' => 
        array (
        ),
        '衡水滨湖新区' => 
        array (
        ),
        '深州市' => 
        array (
        ),
      ),
    ),
    '山西' => 
    array (
      '太原' => 
      array (
        '小店区' => 
        array (
        ),
        '迎泽区' => 
        array (
        ),
        '杏花岭区' => 
        array (
        ),
        '尖草坪区' => 
        array (
        ),
        '万柏林区' => 
        array (
        ),
        '晋源区' => 
        array (
        ),
        '清徐县' => 
        array (
        ),
        '阳曲县' => 
        array (
        ),
        '娄烦县' => 
        array (
        ),
        '山西转型综合改革示范区' => 
        array (
        ),
        '古交市' => 
        array (
        ),
      ),
      '大同' => 
      array (
        '新荣区' => 
        array (
        ),
        '平城区' => 
        array (
        ),
        '云冈区' => 
        array (
        ),
        '云州区' => 
        array (
        ),
        '阳高县' => 
        array (
        ),
        '天镇县' => 
        array (
        ),
        '广灵县' => 
        array (
        ),
        '灵丘县' => 
        array (
        ),
        '浑源县' => 
        array (
        ),
        '左云县' => 
        array (
        ),
        '山西大同经济开发区' => 
        array (
        ),
      ),
      '阳泉' => 
      array (
        '城区' => 
        array (
        ),
        '矿区' => 
        array (
        ),
        '郊区' => 
        array (
        ),
        '平定县' => 
        array (
        ),
        '盂县' => 
        array (
        ),
      ),
      '长治' => 
      array (
        '潞州区' => 
        array (
        ),
        '上党区' => 
        array (
        ),
        '屯留区' => 
        array (
        ),
        '潞城区' => 
        array (
        ),
        '襄垣县' => 
        array (
        ),
        '平顺县' => 
        array (
        ),
        '黎城县' => 
        array (
        ),
        '壶关县' => 
        array (
        ),
        '长子县' => 
        array (
        ),
        '武乡县' => 
        array (
        ),
        '沁县' => 
        array (
        ),
        '沁源县' => 
        array (
        ),
      ),
      '晋城' => 
      array (
        '城区' => 
        array (
        ),
        '沁水县' => 
        array (
        ),
        '阳城县' => 
        array (
        ),
        '陵川县' => 
        array (
        ),
        '泽州县' => 
        array (
        ),
        '高平市' => 
        array (
        ),
      ),
      '朔州' => 
      array (
        '朔城区' => 
        array (
        ),
        '平鲁区' => 
        array (
        ),
        '山阴县' => 
        array (
        ),
        '应县' => 
        array (
        ),
        '右玉县' => 
        array (
        ),
        '山西朔州经济开发区' => 
        array (
        ),
        '怀仁市' => 
        array (
        ),
      ),
      '晋中' => 
      array (
        '榆次区' => 
        array (
        ),
        '太谷区' => 
        array (
        ),
        '榆社县' => 
        array (
        ),
        '左权县' => 
        array (
        ),
        '和顺县' => 
        array (
        ),
        '昔阳县' => 
        array (
        ),
        '寿阳县' => 
        array (
        ),
        '祁县' => 
        array (
        ),
        '平遥县' => 
        array (
        ),
        '灵石县' => 
        array (
        ),
        '介休市' => 
        array (
        ),
      ),
      '运城' => 
      array (
        '盐湖区' => 
        array (
        ),
        '临猗县' => 
        array (
        ),
        '万荣县' => 
        array (
        ),
        '闻喜县' => 
        array (
        ),
        '稷山县' => 
        array (
        ),
        '新绛县' => 
        array (
        ),
        '绛县' => 
        array (
        ),
        '垣曲县' => 
        array (
        ),
        '夏县' => 
        array (
        ),
        '平陆县' => 
        array (
        ),
        '芮城县' => 
        array (
        ),
        '永济市' => 
        array (
        ),
        '河津市' => 
        array (
        ),
      ),
      '忻州' => 
      array (
        '忻府区' => 
        array (
        ),
        '定襄县' => 
        array (
        ),
        '五台县' => 
        array (
        ),
        '代县' => 
        array (
        ),
        '繁峙县' => 
        array (
        ),
        '宁武县' => 
        array (
        ),
        '静乐县' => 
        array (
        ),
        '神池县' => 
        array (
        ),
        '五寨县' => 
        array (
        ),
        '岢岚县' => 
        array (
        ),
        '河曲县' => 
        array (
        ),
        '保德县' => 
        array (
        ),
        '偏关县' => 
        array (
        ),
        '五台山风景名胜区' => 
        array (
        ),
        '原平市' => 
        array (
        ),
      ),
      '临汾' => 
      array (
        '尧都区' => 
        array (
        ),
        '曲沃县' => 
        array (
        ),
        '翼城县' => 
        array (
        ),
        '襄汾县' => 
        array (
        ),
        '洪洞县' => 
        array (
        ),
        '古县' => 
        array (
        ),
        '安泽县' => 
        array (
        ),
        '浮山县' => 
        array (
        ),
        '吉县' => 
        array (
        ),
        '乡宁县' => 
        array (
        ),
        '大宁县' => 
        array (
        ),
        '隰县' => 
        array (
        ),
        '永和县' => 
        array (
        ),
        '蒲县' => 
        array (
        ),
        '汾西县' => 
        array (
        ),
        '侯马市' => 
        array (
        ),
        '霍州市' => 
        array (
        ),
      ),
      '吕梁' => 
      array (
        '离石区' => 
        array (
        ),
        '文水县' => 
        array (
        ),
        '交城县' => 
        array (
        ),
        '兴县' => 
        array (
        ),
        '临县' => 
        array (
        ),
        '柳林县' => 
        array (
        ),
        '石楼县' => 
        array (
        ),
        '岚县' => 
        array (
        ),
        '方山县' => 
        array (
        ),
        '中阳县' => 
        array (
        ),
        '交口县' => 
        array (
        ),
        '孝义市' => 
        array (
        ),
        '汾阳市' => 
        array (
        ),
      ),
    ),
    '内蒙古' => 
    array (
      '呼和浩特' => 
      array (
        '新城区' => 
        array (
        ),
        '回民区' => 
        array (
        ),
        '玉泉区' => 
        array (
        ),
        '赛罕区' => 
        array (
        ),
        '土默特左旗' => 
        array (
        ),
        '托克托县' => 
        array (
        ),
        '和林格尔县' => 
        array (
        ),
        '清水河县' => 
        array (
        ),
        '武川县' => 
        array (
        ),
        '呼和浩特经济技术开发区' => 
        array (
        ),
      ),
      '包头' => 
      array (
        '东河区' => 
        array (
        ),
        '昆都仑区' => 
        array (
        ),
        '青山区' => 
        array (
        ),
        '石拐区' => 
        array (
        ),
        '白云鄂博矿区' => 
        array (
        ),
        '九原区' => 
        array (
        ),
        '土默特右旗' => 
        array (
        ),
        '固阳县' => 
        array (
        ),
        '达尔罕茂明安联合旗' => 
        array (
        ),
        '包头稀土高新技术产业开发区' => 
        array (
        ),
      ),
      '乌海' => 
      array (
        '海勃湾区' => 
        array (
        ),
        '海南区' => 
        array (
        ),
        '乌达区' => 
        array (
        ),
      ),
      '赤峰' => 
      array (
        '红山区' => 
        array (
        ),
        '元宝山区' => 
        array (
        ),
        '松山区' => 
        array (
        ),
        '阿鲁科尔沁旗' => 
        array (
        ),
        '巴林左旗' => 
        array (
        ),
        '巴林右旗' => 
        array (
        ),
        '林西县' => 
        array (
        ),
        '克什克腾旗' => 
        array (
        ),
        '翁牛特旗' => 
        array (
        ),
        '喀喇沁旗' => 
        array (
        ),
        '宁城县' => 
        array (
        ),
        '敖汉旗' => 
        array (
        ),
      ),
      '通辽' => 
      array (
        '科尔沁区' => 
        array (
        ),
        '科尔沁左翼中旗' => 
        array (
        ),
        '科尔沁左翼后旗' => 
        array (
        ),
        '开鲁县' => 
        array (
        ),
        '库伦旗' => 
        array (
        ),
        '奈曼旗' => 
        array (
        ),
        '扎鲁特旗' => 
        array (
        ),
        '通辽经济技术开发区' => 
        array (
        ),
        '霍林郭勒市' => 
        array (
        ),
      ),
      '鄂尔多斯' => 
      array (
        '东胜区' => 
        array (
        ),
        '康巴什区' => 
        array (
        ),
        '达拉特旗' => 
        array (
        ),
        '准格尔旗' => 
        array (
        ),
        '鄂托克前旗' => 
        array (
        ),
        '鄂托克旗' => 
        array (
        ),
        '杭锦旗' => 
        array (
        ),
        '乌审旗' => 
        array (
        ),
        '伊金霍洛旗' => 
        array (
        ),
      ),
      '呼伦贝尔' => 
      array (
        '海拉尔区' => 
        array (
        ),
        '扎赉诺尔区' => 
        array (
        ),
        '阿荣旗' => 
        array (
        ),
        '莫力达瓦达斡尔族自治旗' => 
        array (
        ),
        '鄂伦春自治旗' => 
        array (
        ),
        '鄂温克族自治旗' => 
        array (
        ),
        '陈巴尔虎旗' => 
        array (
        ),
        '新巴尔虎左旗' => 
        array (
        ),
        '新巴尔虎右旗' => 
        array (
        ),
        '满洲里市' => 
        array (
        ),
        '牙克石市' => 
        array (
        ),
        '扎兰屯市' => 
        array (
        ),
        '额尔古纳市' => 
        array (
        ),
        '根河市' => 
        array (
        ),
      ),
      '巴彦淖尔' => 
      array (
        '临河区' => 
        array (
        ),
        '五原县' => 
        array (
        ),
        '磴口县' => 
        array (
        ),
        '乌拉特前旗' => 
        array (
        ),
        '乌拉特中旗' => 
        array (
        ),
        '乌拉特后旗' => 
        array (
        ),
        '杭锦后旗' => 
        array (
        ),
      ),
      '乌兰察布' => 
      array (
        '集宁区' => 
        array (
        ),
        '卓资县' => 
        array (
        ),
        '化德县' => 
        array (
        ),
        '商都县' => 
        array (
        ),
        '兴和县' => 
        array (
        ),
        '凉城县' => 
        array (
        ),
        '察哈尔右翼前旗' => 
        array (
        ),
        '察哈尔右翼中旗' => 
        array (
        ),
        '察哈尔右翼后旗' => 
        array (
        ),
        '四子王旗' => 
        array (
        ),
        '丰镇市' => 
        array (
        ),
      ),
      '兴安盟' => 
      array (
        '乌兰浩特市' => 
        array (
        ),
        '阿尔山市' => 
        array (
        ),
        '科尔沁右翼前旗' => 
        array (
        ),
        '科尔沁右翼中旗' => 
        array (
        ),
        '扎赉特旗' => 
        array (
        ),
        '突泉县' => 
        array (
        ),
      ),
      '锡林郭勒盟' => 
      array (
        '二连浩特市' => 
        array (
        ),
        '锡林浩特市' => 
        array (
        ),
        '阿巴嘎旗' => 
        array (
        ),
        '苏尼特左旗' => 
        array (
        ),
        '苏尼特右旗' => 
        array (
        ),
        '东乌珠穆沁旗' => 
        array (
        ),
        '西乌珠穆沁旗' => 
        array (
        ),
        '太仆寺旗' => 
        array (
        ),
        '镶黄旗' => 
        array (
        ),
        '正镶白旗' => 
        array (
        ),
        '正蓝旗' => 
        array (
        ),
        '多伦县' => 
        array (
        ),
        '乌拉盖管理区管委会' => 
        array (
        ),
      ),
      '阿拉善盟' => 
      array (
        '阿拉善左旗' => 
        array (
        ),
        '阿拉善右旗' => 
        array (
        ),
        '额济纳旗' => 
        array (
        ),
        '内蒙古阿拉善高新技术产业开发区' => 
        array (
        ),
      ),
    ),
    '辽宁' => 
    array (
      '沈阳' => 
      array (
        '和平区' => 
        array (
        ),
        '沈河区' => 
        array (
        ),
        '大东区' => 
        array (
        ),
        '皇姑区' => 
        array (
        ),
        '铁西区' => 
        array (
        ),
        '苏家屯区' => 
        array (
        ),
        '浑南区' => 
        array (
        ),
        '沈北新区' => 
        array (
        ),
        '于洪区' => 
        array (
        ),
        '辽中区' => 
        array (
        ),
        '康平县' => 
        array (
        ),
        '法库县' => 
        array (
        ),
        '新民市' => 
        array (
        ),
      ),
      '大连' => 
      array (
        '中山区' => 
        array (
        ),
        '西岗区' => 
        array (
        ),
        '沙河口区' => 
        array (
        ),
        '甘井子区' => 
        array (
        ),
        '旅顺口区' => 
        array (
        ),
        '金州区' => 
        array (
        ),
        '普兰店区' => 
        array (
        ),
        '长海县' => 
        array (
        ),
        '瓦房店市' => 
        array (
        ),
        '庄河市' => 
        array (
        ),
      ),
      '鞍山' => 
      array (
        '铁东区' => 
        array (
        ),
        '铁西区' => 
        array (
        ),
        '立山区' => 
        array (
        ),
        '千山区' => 
        array (
        ),
        '台安县' => 
        array (
        ),
        '岫岩满族自治县' => 
        array (
        ),
        '海城市' => 
        array (
        ),
      ),
      '抚顺' => 
      array (
        '新抚区' => 
        array (
        ),
        '东洲区' => 
        array (
        ),
        '望花区' => 
        array (
        ),
        '顺城区' => 
        array (
        ),
        '抚顺县' => 
        array (
        ),
        '新宾满族自治县' => 
        array (
        ),
        '清原满族自治县' => 
        array (
        ),
      ),
      '本溪' => 
      array (
        '平山区' => 
        array (
        ),
        '溪湖区' => 
        array (
        ),
        '明山区' => 
        array (
        ),
        '南芬区' => 
        array (
        ),
        '本溪满族自治县' => 
        array (
        ),
        '桓仁满族自治县' => 
        array (
        ),
      ),
      '丹东' => 
      array (
        '元宝区' => 
        array (
        ),
        '振兴区' => 
        array (
        ),
        '振安区' => 
        array (
        ),
        '宽甸满族自治县' => 
        array (
        ),
        '东港市' => 
        array (
        ),
        '凤城市' => 
        array (
        ),
      ),
      '锦州' => 
      array (
        '古塔区' => 
        array (
        ),
        '凌河区' => 
        array (
        ),
        '太和区' => 
        array (
        ),
        '黑山县' => 
        array (
        ),
        '义县' => 
        array (
        ),
        '凌海市' => 
        array (
        ),
        '北镇市' => 
        array (
        ),
      ),
      '营口' => 
      array (
        '站前区' => 
        array (
        ),
        '西市区' => 
        array (
        ),
        '鲅鱼圈区' => 
        array (
        ),
        '老边区' => 
        array (
        ),
        '盖州市' => 
        array (
        ),
        '大石桥市' => 
        array (
        ),
      ),
      '阜新' => 
      array (
        '海州区' => 
        array (
        ),
        '新邱区' => 
        array (
        ),
        '太平区' => 
        array (
        ),
        '清河门区' => 
        array (
        ),
        '细河区' => 
        array (
        ),
        '阜新蒙古族自治县' => 
        array (
        ),
        '彰武县' => 
        array (
        ),
      ),
      '辽阳' => 
      array (
        '白塔区' => 
        array (
        ),
        '文圣区' => 
        array (
        ),
        '宏伟区' => 
        array (
        ),
        '弓长岭区' => 
        array (
        ),
        '太子河区' => 
        array (
        ),
        '辽阳县' => 
        array (
        ),
        '灯塔市' => 
        array (
        ),
      ),
      '盘锦' => 
      array (
        '双台子区' => 
        array (
        ),
        '兴隆台区' => 
        array (
        ),
        '大洼区' => 
        array (
        ),
        '盘山县' => 
        array (
        ),
      ),
      '铁岭' => 
      array (
        '银州区' => 
        array (
        ),
        '清河区' => 
        array (
        ),
        '铁岭县' => 
        array (
        ),
        '西丰县' => 
        array (
        ),
        '昌图县' => 
        array (
        ),
        '调兵山市' => 
        array (
        ),
        '开原市' => 
        array (
        ),
      ),
      '朝阳' => 
      array (
        '双塔区' => 
        array (
        ),
        '龙城区' => 
        array (
        ),
        '朝阳县' => 
        array (
        ),
        '建平县' => 
        array (
        ),
        '喀喇沁左翼蒙古族自治县' => 
        array (
        ),
        '北票市' => 
        array (
        ),
        '凌源市' => 
        array (
        ),
      ),
      '葫芦岛' => 
      array (
        '连山区' => 
        array (
        ),
        '龙港区' => 
        array (
        ),
        '南票区' => 
        array (
        ),
        '绥中县' => 
        array (
        ),
        '建昌县' => 
        array (
        ),
        '兴城市' => 
        array (
        ),
      ),
    ),
    '吉林' => 
    array (
      '长春' => 
      array (
        '南关区' => 
        array (
        ),
        '宽城区' => 
        array (
        ),
        '朝阳区' => 
        array (
        ),
        '二道区' => 
        array (
        ),
        '绿园区' => 
        array (
        ),
        '双阳区' => 
        array (
        ),
        '九台区' => 
        array (
        ),
        '农安县' => 
        array (
        ),
        '长春经济技术开发区' => 
        array (
        ),
        '长春净月高新技术产业开发区' => 
        array (
        ),
        '长春高新技术产业开发区' => 
        array (
        ),
        '长春汽车经济技术开发区' => 
        array (
        ),
        '榆树市' => 
        array (
        ),
        '德惠市' => 
        array (
        ),
        '公主岭市' => 
        array (
        ),
      ),
      '吉林' => 
      array (
        '昌邑区' => 
        array (
        ),
        '龙潭区' => 
        array (
        ),
        '船营区' => 
        array (
        ),
        '丰满区' => 
        array (
        ),
        '永吉县' => 
        array (
        ),
        '吉林经济开发区' => 
        array (
        ),
        '吉林高新技术产业开发区' => 
        array (
        ),
        '吉林中国新加坡食品区' => 
        array (
        ),
        '蛟河市' => 
        array (
        ),
        '桦甸市' => 
        array (
        ),
        '舒兰市' => 
        array (
        ),
        '磐石市' => 
        array (
        ),
      ),
      '四平' => 
      array (
        '铁西区' => 
        array (
        ),
        '铁东区' => 
        array (
        ),
        '梨树县' => 
        array (
        ),
        '伊通满族自治县' => 
        array (
        ),
        '双辽市' => 
        array (
        ),
      ),
      '辽源' => 
      array (
        '龙山区' => 
        array (
        ),
        '西安区' => 
        array (
        ),
        '东丰县' => 
        array (
        ),
        '东辽县' => 
        array (
        ),
      ),
      '通化' => 
      array (
        '东昌区' => 
        array (
        ),
        '二道江区' => 
        array (
        ),
        '通化县' => 
        array (
        ),
        '辉南县' => 
        array (
        ),
        '柳河县' => 
        array (
        ),
        '梅河口市' => 
        array (
        ),
        '集安市' => 
        array (
        ),
      ),
      '白山' => 
      array (
        '浑江区' => 
        array (
        ),
        '江源区' => 
        array (
        ),
        '抚松县' => 
        array (
        ),
        '靖宇县' => 
        array (
        ),
        '长白朝鲜族自治县' => 
        array (
        ),
        '临江市' => 
        array (
        ),
      ),
      '松原' => 
      array (
        '宁江区' => 
        array (
        ),
        '前郭尔罗斯蒙古族自治县' => 
        array (
        ),
        '长岭县' => 
        array (
        ),
        '乾安县' => 
        array (
        ),
        '吉林松原经济开发区' => 
        array (
        ),
        '扶余市' => 
        array (
        ),
      ),
      '白城' => 
      array (
        '洮北区' => 
        array (
        ),
        '镇赉县' => 
        array (
        ),
        '通榆县' => 
        array (
        ),
        '吉林白城经济开发区' => 
        array (
        ),
        '洮南市' => 
        array (
        ),
        '大安市' => 
        array (
        ),
      ),
      '延边朝鲜族自治州' => 
      array (
        '延吉市' => 
        array (
        ),
        '图们市' => 
        array (
        ),
        '敦化市' => 
        array (
        ),
        '珲春市' => 
        array (
        ),
        '龙井市' => 
        array (
        ),
        '和龙市' => 
        array (
        ),
        '汪清县' => 
        array (
        ),
        '安图县' => 
        array (
        ),
      ),
    ),
    '黑龙江' => 
    array (
      '哈尔滨' => 
      array (
        '道里区' => 
        array (
        ),
        '南岗区' => 
        array (
        ),
        '道外区' => 
        array (
        ),
        '平房区' => 
        array (
        ),
        '松北区' => 
        array (
        ),
        '香坊区' => 
        array (
        ),
        '呼兰区' => 
        array (
        ),
        '阿城区' => 
        array (
        ),
        '双城区' => 
        array (
        ),
        '依兰县' => 
        array (
        ),
        '方正县' => 
        array (
        ),
        '宾县' => 
        array (
        ),
        '巴彦县' => 
        array (
        ),
        '木兰县' => 
        array (
        ),
        '通河县' => 
        array (
        ),
        '延寿县' => 
        array (
        ),
        '尚志市' => 
        array (
        ),
        '五常市' => 
        array (
        ),
      ),
      '齐齐哈尔' => 
      array (
        '龙沙区' => 
        array (
        ),
        '建华区' => 
        array (
        ),
        '铁锋区' => 
        array (
        ),
        '昂昂溪区' => 
        array (
        ),
        '富拉尔基区' => 
        array (
        ),
        '碾子山区' => 
        array (
        ),
        '梅里斯达斡尔族区' => 
        array (
        ),
        '龙江县' => 
        array (
        ),
        '依安县' => 
        array (
        ),
        '泰来县' => 
        array (
        ),
        '甘南县' => 
        array (
        ),
        '富裕县' => 
        array (
        ),
        '克山县' => 
        array (
        ),
        '克东县' => 
        array (
        ),
        '拜泉县' => 
        array (
        ),
        '讷河市' => 
        array (
        ),
      ),
      '鸡西' => 
      array (
        '鸡冠区' => 
        array (
        ),
        '恒山区' => 
        array (
        ),
        '滴道区' => 
        array (
        ),
        '梨树区' => 
        array (
        ),
        '城子河区' => 
        array (
        ),
        '麻山区' => 
        array (
        ),
        '鸡东县' => 
        array (
        ),
        '虎林市' => 
        array (
        ),
        '密山市' => 
        array (
        ),
      ),
      '鹤岗' => 
      array (
        '向阳区' => 
        array (
        ),
        '工农区' => 
        array (
        ),
        '南山区' => 
        array (
        ),
        '兴安区' => 
        array (
        ),
        '东山区' => 
        array (
        ),
        '兴山区' => 
        array (
        ),
        '萝北县' => 
        array (
        ),
        '绥滨县' => 
        array (
        ),
      ),
      '双鸭山' => 
      array (
        '尖山区' => 
        array (
        ),
        '岭东区' => 
        array (
        ),
        '四方台区' => 
        array (
        ),
        '宝山区' => 
        array (
        ),
        '集贤县' => 
        array (
        ),
        '友谊县' => 
        array (
        ),
        '宝清县' => 
        array (
        ),
        '饶河县' => 
        array (
        ),
      ),
      '大庆' => 
      array (
        '萨尔图区' => 
        array (
        ),
        '龙凤区' => 
        array (
        ),
        '让胡路区' => 
        array (
        ),
        '红岗区' => 
        array (
        ),
        '大同区' => 
        array (
        ),
        '肇州县' => 
        array (
        ),
        '肇源县' => 
        array (
        ),
        '林甸县' => 
        array (
        ),
        '杜尔伯特蒙古族自治县' => 
        array (
        ),
        '大庆高新技术产业开发区' => 
        array (
        ),
      ),
      '伊春' => 
      array (
        '伊美区' => 
        array (
        ),
        '乌翠区' => 
        array (
        ),
        '友好区' => 
        array (
        ),
        '嘉荫县' => 
        array (
        ),
        '汤旺县' => 
        array (
        ),
        '丰林县' => 
        array (
        ),
        '大箐山县' => 
        array (
        ),
        '南岔县' => 
        array (
        ),
        '金林区' => 
        array (
        ),
        '铁力市' => 
        array (
        ),
      ),
      '佳木斯' => 
      array (
        '向阳区' => 
        array (
        ),
        '前进区' => 
        array (
        ),
        '东风区' => 
        array (
        ),
        '郊区' => 
        array (
        ),
        '桦南县' => 
        array (
        ),
        '桦川县' => 
        array (
        ),
        '汤原县' => 
        array (
        ),
        '同江市' => 
        array (
        ),
        '富锦市' => 
        array (
        ),
        '抚远市' => 
        array (
        ),
      ),
      '七台河' => 
      array (
        '新兴区' => 
        array (
        ),
        '桃山区' => 
        array (
        ),
        '茄子河区' => 
        array (
        ),
        '勃利县' => 
        array (
        ),
      ),
      '牡丹江' => 
      array (
        '东安区' => 
        array (
        ),
        '阳明区' => 
        array (
        ),
        '爱民区' => 
        array (
        ),
        '西安区' => 
        array (
        ),
        '林口县' => 
        array (
        ),
        '绥芬河市' => 
        array (
        ),
        '海林市' => 
        array (
        ),
        '宁安市' => 
        array (
        ),
        '穆棱市' => 
        array (
        ),
        '东宁市' => 
        array (
        ),
      ),
      '黑河' => 
      array (
        '爱辉区' => 
        array (
        ),
        '逊克县' => 
        array (
        ),
        '孙吴县' => 
        array (
        ),
        '北安市' => 
        array (
        ),
        '五大连池市' => 
        array (
        ),
        '嫩江市' => 
        array (
        ),
      ),
      '绥化' => 
      array (
        '北林区' => 
        array (
        ),
        '望奎县' => 
        array (
        ),
        '兰西县' => 
        array (
        ),
        '青冈县' => 
        array (
        ),
        '庆安县' => 
        array (
        ),
        '明水县' => 
        array (
        ),
        '绥棱县' => 
        array (
        ),
        '安达市' => 
        array (
        ),
        '肇东市' => 
        array (
        ),
        '海伦市' => 
        array (
        ),
      ),
      '大兴安岭地区' => 
      array (
        '漠河市' => 
        array (
        ),
        '呼玛县' => 
        array (
        ),
        '塔河县' => 
        array (
        ),
        '加格达奇区' => 
        array (
        ),
        '松岭区' => 
        array (
        ),
        '新林区' => 
        array (
        ),
        '呼中区' => 
        array (
        ),
      ),
    ),
    '上海' => 
    array (
      '黄浦区' => 
      array (
      ),
      '徐汇区' => 
      array (
      ),
      '长宁区' => 
      array (
      ),
      '静安区' => 
      array (
      ),
      '普陀区' => 
      array (
      ),
      '虹口区' => 
      array (
      ),
      '杨浦区' => 
      array (
      ),
      '闵行区' => 
      array (
      ),
      '宝山区' => 
      array (
      ),
      '嘉定区' => 
      array (
      ),
      '浦东新区' => 
      array (
      ),
      '金山区' => 
      array (
      ),
      '松江区' => 
      array (
      ),
      '青浦区' => 
      array (
      ),
      '奉贤区' => 
      array (
      ),
      '崇明区' => 
      array (
      ),
    ),
    '江苏' => 
    array (
      '南京' => 
      array (
        '玄武区' => 
        array (
        ),
        '秦淮区' => 
        array (
        ),
        '建邺区' => 
        array (
        ),
        '鼓楼区' => 
        array (
        ),
        '浦口区' => 
        array (
        ),
        '栖霞区' => 
        array (
        ),
        '雨花台区' => 
        array (
        ),
        '江宁区' => 
        array (
        ),
        '六合区' => 
        array (
        ),
        '溧水区' => 
        array (
        ),
        '高淳区' => 
        array (
        ),
      ),
      '无锡' => 
      array (
        '锡山区' => 
        array (
        ),
        '惠山区' => 
        array (
        ),
        '滨湖区' => 
        array (
        ),
        '梁溪区' => 
        array (
        ),
        '新吴区' => 
        array (
        ),
        '江阴市' => 
        array (
        ),
        '宜兴市' => 
        array (
        ),
      ),
      '徐州' => 
      array (
        '鼓楼区' => 
        array (
        ),
        '云龙区' => 
        array (
        ),
        '贾汪区' => 
        array (
        ),
        '泉山区' => 
        array (
        ),
        '铜山区' => 
        array (
        ),
        '丰县' => 
        array (
        ),
        '沛县' => 
        array (
        ),
        '睢宁县' => 
        array (
        ),
        '徐州经济技术开发区' => 
        array (
        ),
        '新沂市' => 
        array (
        ),
        '邳州市' => 
        array (
        ),
      ),
      '常州' => 
      array (
        '天宁区' => 
        array (
        ),
        '钟楼区' => 
        array (
        ),
        '新北区' => 
        array (
        ),
        '武进区' => 
        array (
        ),
        '金坛区' => 
        array (
        ),
        '溧阳市' => 
        array (
        ),
      ),
      '苏州' => 
      array (
        '虎丘区' => 
        array (
        ),
        '吴中区' => 
        array (
        ),
        '相城区' => 
        array (
        ),
        '姑苏区' => 
        array (
        ),
        '吴江区' => 
        array (
        ),
        '苏州工业园区' => 
        array (
        ),
        '常熟市' => 
        array (
        ),
        '张家港市' => 
        array (
        ),
        '昆山市' => 
        array (
        ),
        '太仓市' => 
        array (
        ),
      ),
      '南通' => 
      array (
        '通州区' => 
        array (
        ),
        '崇川区' => 
        array (
        ),
        '海门区' => 
        array (
        ),
        '如东县' => 
        array (
        ),
        '南通经济技术开发区' => 
        array (
        ),
        '启东市' => 
        array (
        ),
        '如皋市' => 
        array (
        ),
        '海安市' => 
        array (
        ),
      ),
      '连云港' => 
      array (
        '连云区' => 
        array (
        ),
        '海州区' => 
        array (
        ),
        '赣榆区' => 
        array (
        ),
        '东海县' => 
        array (
        ),
        '灌云县' => 
        array (
        ),
        '灌南县' => 
        array (
        ),
        '连云港经济技术开发区' => 
        array (
        ),
      ),
      '淮安' => 
      array (
        '淮安区' => 
        array (
        ),
        '淮阴区' => 
        array (
        ),
        '清江浦区' => 
        array (
        ),
        '洪泽区' => 
        array (
        ),
        '涟水县' => 
        array (
        ),
        '盱眙县' => 
        array (
        ),
        '金湖县' => 
        array (
        ),
        '淮安经济技术开发区' => 
        array (
        ),
      ),
      '盐城' => 
      array (
        '亭湖区' => 
        array (
        ),
        '盐都区' => 
        array (
        ),
        '大丰区' => 
        array (
        ),
        '响水县' => 
        array (
        ),
        '滨海县' => 
        array (
        ),
        '阜宁县' => 
        array (
        ),
        '射阳县' => 
        array (
        ),
        '建湖县' => 
        array (
        ),
        '盐城经济技术开发区' => 
        array (
        ),
        '东台市' => 
        array (
        ),
      ),
      '扬州' => 
      array (
        '广陵区' => 
        array (
        ),
        '邗江区' => 
        array (
        ),
        '江都区' => 
        array (
        ),
        '宝应县' => 
        array (
        ),
        '扬州经济技术开发区' => 
        array (
        ),
        '仪征市' => 
        array (
        ),
        '高邮市' => 
        array (
        ),
      ),
      '镇江' => 
      array (
        '京口区' => 
        array (
        ),
        '润州区' => 
        array (
        ),
        '丹徒区' => 
        array (
        ),
        '镇江新区' => 
        array (
        ),
        '丹阳市' => 
        array (
        ),
        '扬中市' => 
        array (
        ),
        '句容市' => 
        array (
        ),
      ),
      '泰州' => 
      array (
        '海陵区' => 
        array (
        ),
        '高港区' => 
        array (
        ),
        '姜堰区' => 
        array (
        ),
        '兴化市' => 
        array (
        ),
        '靖江市' => 
        array (
        ),
        '泰兴市' => 
        array (
        ),
      ),
      '宿迁' => 
      array (
        '宿城区' => 
        array (
        ),
        '宿豫区' => 
        array (
        ),
        '沭阳县' => 
        array (
        ),
        '泗阳县' => 
        array (
        ),
        '泗洪县' => 
        array (
        ),
        '宿迁经济技术开发区' => 
        array (
        ),
      ),
    ),
    '浙江' => 
    array (
      '杭州' => 
      array (
        '上城区' => 
        array (
        ),
        '拱墅区' => 
        array (
        ),
        '西湖区' => 
        array (
        ),
        '滨江区' => 
        array (
        ),
        '萧山区' => 
        array (
        ),
        '余杭区' => 
        array (
        ),
        '富阳区' => 
        array (
        ),
        '临安区' => 
        array (
        ),
        '临平区' => 
        array (
        ),
        '钱塘区' => 
        array (
        ),
        '桐庐县' => 
        array (
        ),
        '淳安县' => 
        array (
        ),
        '建德市' => 
        array (
        ),
      ),
      '宁波' => 
      array (
        '海曙区' => 
        array (
        ),
        '江北区' => 
        array (
        ),
        '北仑区' => 
        array (
        ),
        '镇海区' => 
        array (
        ),
        '鄞州区' => 
        array (
        ),
        '奉化区' => 
        array (
        ),
        '象山县' => 
        array (
        ),
        '宁海县' => 
        array (
        ),
        '余姚市' => 
        array (
        ),
        '慈溪市' => 
        array (
        ),
      ),
      '温州' => 
      array (
        '鹿城区' => 
        array (
        ),
        '龙湾区' => 
        array (
        ),
        '瓯海区' => 
        array (
        ),
        '洞头区' => 
        array (
        ),
        '永嘉县' => 
        array (
        ),
        '平阳县' => 
        array (
        ),
        '苍南县' => 
        array (
        ),
        '文成县' => 
        array (
        ),
        '泰顺县' => 
        array (
        ),
        '瑞安市' => 
        array (
        ),
        '乐清市' => 
        array (
        ),
        '龙港市' => 
        array (
        ),
      ),
      '嘉兴' => 
      array (
        '南湖区' => 
        array (
        ),
        '秀洲区' => 
        array (
        ),
        '嘉善县' => 
        array (
        ),
        '海盐县' => 
        array (
        ),
        '海宁市' => 
        array (
        ),
        '平湖市' => 
        array (
        ),
        '桐乡市' => 
        array (
        ),
      ),
      '湖州' => 
      array (
        '吴兴区' => 
        array (
        ),
        '南浔区' => 
        array (
        ),
        '德清县' => 
        array (
        ),
        '长兴县' => 
        array (
        ),
        '安吉县' => 
        array (
        ),
      ),
      '绍兴' => 
      array (
        '越城区' => 
        array (
        ),
        '柯桥区' => 
        array (
        ),
        '上虞区' => 
        array (
        ),
        '新昌县' => 
        array (
        ),
        '诸暨市' => 
        array (
        ),
        '嵊州市' => 
        array (
        ),
      ),
      '金华' => 
      array (
        '婺城区' => 
        array (
        ),
        '金东区' => 
        array (
        ),
        '武义县' => 
        array (
        ),
        '浦江县' => 
        array (
        ),
        '磐安县' => 
        array (
        ),
        '兰溪市' => 
        array (
        ),
        '义乌市' => 
        array (
        ),
        '东阳市' => 
        array (
        ),
        '永康市' => 
        array (
        ),
      ),
      '衢州' => 
      array (
        '柯城区' => 
        array (
        ),
        '衢江区' => 
        array (
        ),
        '常山县' => 
        array (
        ),
        '开化县' => 
        array (
        ),
        '龙游县' => 
        array (
        ),
        '江山市' => 
        array (
        ),
      ),
      '舟山' => 
      array (
        '定海区' => 
        array (
        ),
        '普陀区' => 
        array (
        ),
        '岱山县' => 
        array (
        ),
        '嵊泗县' => 
        array (
        ),
      ),
      '台州' => 
      array (
        '椒江区' => 
        array (
        ),
        '黄岩区' => 
        array (
        ),
        '路桥区' => 
        array (
        ),
        '三门县' => 
        array (
        ),
        '天台县' => 
        array (
        ),
        '仙居县' => 
        array (
        ),
        '温岭市' => 
        array (
        ),
        '临海市' => 
        array (
        ),
        '玉环市' => 
        array (
        ),
      ),
      '丽水' => 
      array (
        '莲都区' => 
        array (
        ),
        '青田县' => 
        array (
        ),
        '缙云县' => 
        array (
        ),
        '遂昌县' => 
        array (
        ),
        '松阳县' => 
        array (
        ),
        '云和县' => 
        array (
        ),
        '庆元县' => 
        array (
        ),
        '景宁畲族自治县' => 
        array (
        ),
        '龙泉市' => 
        array (
        ),
      ),
    ),
    '安徽' => 
    array (
      '合肥' => 
      array (
        '瑶海区' => 
        array (
        ),
        '庐阳区' => 
        array (
        ),
        '蜀山区' => 
        array (
        ),
        '包河区' => 
        array (
        ),
        '长丰县' => 
        array (
        ),
        '肥东县' => 
        array (
        ),
        '肥西县' => 
        array (
        ),
        '庐江县' => 
        array (
        ),
        '合肥高新技术产业开发区' => 
        array (
        ),
        '合肥经济技术开发区' => 
        array (
        ),
        '合肥新站高新技术产业开发区' => 
        array (
        ),
        '巢湖市' => 
        array (
        ),
      ),
      '芜湖' => 
      array (
        '镜湖区' => 
        array (
        ),
        '鸠江区' => 
        array (
        ),
        '弋江区' => 
        array (
        ),
        '湾沚区' => 
        array (
        ),
        '繁昌区' => 
        array (
        ),
        '南陵县' => 
        array (
        ),
        '芜湖经济技术开发区' => 
        array (
        ),
        '安徽芜湖三山经济开发区' => 
        array (
        ),
        '无为市' => 
        array (
        ),
      ),
      '蚌埠' => 
      array (
        '龙子湖区' => 
        array (
        ),
        '蚌山区' => 
        array (
        ),
        '禹会区' => 
        array (
        ),
        '淮上区' => 
        array (
        ),
        '怀远县' => 
        array (
        ),
        '五河县' => 
        array (
        ),
        '固镇县' => 
        array (
        ),
        '蚌埠市高新技术开发区' => 
        array (
        ),
        '蚌埠市经济开发区' => 
        array (
        ),
      ),
      '淮南' => 
      array (
        '大通区' => 
        array (
        ),
        '田家庵区' => 
        array (
        ),
        '谢家集区' => 
        array (
        ),
        '八公山区' => 
        array (
        ),
        '潘集区' => 
        array (
        ),
        '凤台县' => 
        array (
        ),
        '寿县' => 
        array (
        ),
      ),
      '马鞍山' => 
      array (
        '花山区' => 
        array (
        ),
        '雨山区' => 
        array (
        ),
        '博望区' => 
        array (
        ),
        '当涂县' => 
        array (
        ),
        '含山县' => 
        array (
        ),
        '和县' => 
        array (
        ),
      ),
      '淮北' => 
      array (
        '杜集区' => 
        array (
        ),
        '相山区' => 
        array (
        ),
        '烈山区' => 
        array (
        ),
        '濉溪县' => 
        array (
        ),
      ),
      '铜陵' => 
      array (
        '铜官区' => 
        array (
        ),
        '义安区' => 
        array (
        ),
        '郊区' => 
        array (
        ),
        '枞阳县' => 
        array (
        ),
      ),
      '安庆' => 
      array (
        '迎江区' => 
        array (
        ),
        '大观区' => 
        array (
        ),
        '宜秀区' => 
        array (
        ),
        '怀宁县' => 
        array (
        ),
        '太湖县' => 
        array (
        ),
        '宿松县' => 
        array (
        ),
        '望江县' => 
        array (
        ),
        '岳西县' => 
        array (
        ),
        '安徽安庆经济开发区' => 
        array (
        ),
        '桐城市' => 
        array (
        ),
        '潜山市' => 
        array (
        ),
      ),
      '黄山' => 
      array (
        '屯溪区' => 
        array (
        ),
        '黄山区' => 
        array (
        ),
        '徽州区' => 
        array (
        ),
        '歙县' => 
        array (
        ),
        '休宁县' => 
        array (
        ),
        '黟县' => 
        array (
        ),
        '祁门县' => 
        array (
        ),
      ),
      '滁州' => 
      array (
        '琅琊区' => 
        array (
        ),
        '南谯区' => 
        array (
        ),
        '来安县' => 
        array (
        ),
        '全椒县' => 
        array (
        ),
        '定远县' => 
        array (
        ),
        '凤阳县' => 
        array (
        ),
        '中新苏滁高新技术产业开发区' => 
        array (
        ),
        '滁州经济技术开发区' => 
        array (
        ),
        '天长市' => 
        array (
        ),
        '明光市' => 
        array (
        ),
      ),
      '阜阳' => 
      array (
        '颍州区' => 
        array (
        ),
        '颍东区' => 
        array (
        ),
        '颍泉区' => 
        array (
        ),
        '临泉县' => 
        array (
        ),
        '太和县' => 
        array (
        ),
        '阜南县' => 
        array (
        ),
        '颍上县' => 
        array (
        ),
        '阜阳合肥现代产业园区' => 
        array (
        ),
        '阜阳经济技术开发区' => 
        array (
        ),
        '界首市' => 
        array (
        ),
      ),
      '宿州' => 
      array (
        '埇桥区' => 
        array (
        ),
        '砀山县' => 
        array (
        ),
        '萧县' => 
        array (
        ),
        '灵璧县' => 
        array (
        ),
        '泗县' => 
        array (
        ),
        '宿州马鞍山现代产业园区' => 
        array (
        ),
        '宿州经济技术开发区' => 
        array (
        ),
      ),
      '六安' => 
      array (
        '金安区' => 
        array (
        ),
        '裕安区' => 
        array (
        ),
        '叶集区' => 
        array (
        ),
        '霍邱县' => 
        array (
        ),
        '舒城县' => 
        array (
        ),
        '金寨县' => 
        array (
        ),
        '霍山县' => 
        array (
        ),
      ),
      '亳州' => 
      array (
        '谯城区' => 
        array (
        ),
        '涡阳县' => 
        array (
        ),
        '蒙城县' => 
        array (
        ),
        '利辛县' => 
        array (
        ),
      ),
      '池州' => 
      array (
        '贵池区' => 
        array (
        ),
        '东至县' => 
        array (
        ),
        '石台县' => 
        array (
        ),
        '青阳县' => 
        array (
        ),
      ),
      '宣城' => 
      array (
        '宣州区' => 
        array (
        ),
        '郎溪县' => 
        array (
        ),
        '泾县' => 
        array (
        ),
        '绩溪县' => 
        array (
        ),
        '旌德县' => 
        array (
        ),
        '宣城市经济开发区' => 
        array (
        ),
        '宁国市' => 
        array (
        ),
        '广德市' => 
        array (
        ),
      ),
    ),
    '福建' => 
    array (
      '福州' => 
      array (
        '鼓楼区' => 
        array (
        ),
        '台江区' => 
        array (
        ),
        '仓山区' => 
        array (
        ),
        '马尾区' => 
        array (
        ),
        '晋安区' => 
        array (
        ),
        '长乐区' => 
        array (
        ),
        '闽侯县' => 
        array (
        ),
        '连江县' => 
        array (
        ),
        '罗源县' => 
        array (
        ),
        '闽清县' => 
        array (
        ),
        '永泰县' => 
        array (
        ),
        '平潭县' => 
        array (
        ),
        '福清市' => 
        array (
        ),
      ),
      '厦门' => 
      array (
        '思明区' => 
        array (
        ),
        '海沧区' => 
        array (
        ),
        '湖里区' => 
        array (
        ),
        '集美区' => 
        array (
        ),
        '同安区' => 
        array (
        ),
        '翔安区' => 
        array (
        ),
      ),
      '莆田' => 
      array (
        '城厢区' => 
        array (
        ),
        '涵江区' => 
        array (
        ),
        '荔城区' => 
        array (
        ),
        '秀屿区' => 
        array (
        ),
        '仙游县' => 
        array (
        ),
      ),
      '三明' => 
      array (
        '三元区' => 
        array (
        ),
        '沙县区' => 
        array (
        ),
        '明溪县' => 
        array (
        ),
        '清流县' => 
        array (
        ),
        '宁化县' => 
        array (
        ),
        '大田县' => 
        array (
        ),
        '尤溪县' => 
        array (
        ),
        '将乐县' => 
        array (
        ),
        '泰宁县' => 
        array (
        ),
        '建宁县' => 
        array (
        ),
        '永安市' => 
        array (
        ),
      ),
      '泉州' => 
      array (
        '鲤城区' => 
        array (
        ),
        '丰泽区' => 
        array (
        ),
        '洛江区' => 
        array (
        ),
        '泉港区' => 
        array (
        ),
        '惠安县' => 
        array (
        ),
        '安溪县' => 
        array (
        ),
        '永春县' => 
        array (
        ),
        '德化县' => 
        array (
        ),
        '金门县' => 
        array (
        ),
        '石狮市' => 
        array (
        ),
        '晋江市' => 
        array (
        ),
        '南安市' => 
        array (
        ),
      ),
      '漳州' => 
      array (
        '芗城区' => 
        array (
        ),
        '龙文区' => 
        array (
        ),
        '龙海区' => 
        array (
        ),
        '长泰区' => 
        array (
        ),
        '云霄县' => 
        array (
        ),
        '漳浦县' => 
        array (
        ),
        '诏安县' => 
        array (
        ),
        '东山县' => 
        array (
        ),
        '南靖县' => 
        array (
        ),
        '平和县' => 
        array (
        ),
        '华安县' => 
        array (
        ),
      ),
      '南平' => 
      array (
        '延平区' => 
        array (
        ),
        '建阳区' => 
        array (
        ),
        '顺昌县' => 
        array (
        ),
        '浦城县' => 
        array (
        ),
        '光泽县' => 
        array (
        ),
        '松溪县' => 
        array (
        ),
        '政和县' => 
        array (
        ),
        '邵武市' => 
        array (
        ),
        '武夷山市' => 
        array (
        ),
        '建瓯市' => 
        array (
        ),
      ),
      '龙岩' => 
      array (
        '新罗区' => 
        array (
        ),
        '永定区' => 
        array (
        ),
        '长汀县' => 
        array (
        ),
        '上杭县' => 
        array (
        ),
        '武平县' => 
        array (
        ),
        '连城县' => 
        array (
        ),
        '漳平市' => 
        array (
        ),
      ),
      '宁德' => 
      array (
        '蕉城区' => 
        array (
        ),
        '霞浦县' => 
        array (
        ),
        '古田县' => 
        array (
        ),
        '屏南县' => 
        array (
        ),
        '寿宁县' => 
        array (
        ),
        '周宁县' => 
        array (
        ),
        '柘荣县' => 
        array (
        ),
        '福安市' => 
        array (
        ),
        '福鼎市' => 
        array (
        ),
      ),
    ),
    '江西' => 
    array (
      '南昌' => 
      array (
        '东湖区' => 
        array (
        ),
        '西湖区' => 
        array (
        ),
        '青云谱区' => 
        array (
        ),
        '青山湖区' => 
        array (
        ),
        '新建区' => 
        array (
        ),
        '红谷滩区' => 
        array (
        ),
        '南昌县' => 
        array (
        ),
        '安义县' => 
        array (
        ),
        '进贤县' => 
        array (
        ),
      ),
      '景德镇' => 
      array (
        '昌江区' => 
        array (
        ),
        '珠山区' => 
        array (
        ),
        '浮梁县' => 
        array (
        ),
        '乐平市' => 
        array (
        ),
      ),
      '萍乡' => 
      array (
        '安源区' => 
        array (
        ),
        '湘东区' => 
        array (
        ),
        '莲花县' => 
        array (
        ),
        '上栗县' => 
        array (
        ),
        '芦溪县' => 
        array (
        ),
      ),
      '九江' => 
      array (
        '濂溪区' => 
        array (
        ),
        '浔阳区' => 
        array (
        ),
        '柴桑区' => 
        array (
        ),
        '武宁县' => 
        array (
        ),
        '修水县' => 
        array (
        ),
        '永修县' => 
        array (
        ),
        '德安县' => 
        array (
        ),
        '都昌县' => 
        array (
        ),
        '湖口县' => 
        array (
        ),
        '彭泽县' => 
        array (
        ),
        '瑞昌市' => 
        array (
        ),
        '共青城市' => 
        array (
        ),
        '庐山市' => 
        array (
        ),
      ),
      '新余' => 
      array (
        '渝水区' => 
        array (
        ),
        '分宜县' => 
        array (
        ),
      ),
      '鹰潭' => 
      array (
        '月湖区' => 
        array (
        ),
        '余江区' => 
        array (
        ),
        '贵溪市' => 
        array (
        ),
      ),
      '赣州' => 
      array (
        '章贡区' => 
        array (
        ),
        '南康区' => 
        array (
        ),
        '赣县区' => 
        array (
        ),
        '信丰县' => 
        array (
        ),
        '大余县' => 
        array (
        ),
        '上犹县' => 
        array (
        ),
        '崇义县' => 
        array (
        ),
        '安远县' => 
        array (
        ),
        '定南县' => 
        array (
        ),
        '全南县' => 
        array (
        ),
        '宁都县' => 
        array (
        ),
        '于都县' => 
        array (
        ),
        '兴国县' => 
        array (
        ),
        '会昌县' => 
        array (
        ),
        '寻乌县' => 
        array (
        ),
        '石城县' => 
        array (
        ),
        '瑞金市' => 
        array (
        ),
        '龙南市' => 
        array (
        ),
      ),
      '吉安' => 
      array (
        '吉州区' => 
        array (
        ),
        '青原区' => 
        array (
        ),
        '吉安县' => 
        array (
        ),
        '吉水县' => 
        array (
        ),
        '峡江县' => 
        array (
        ),
        '新干县' => 
        array (
        ),
        '永丰县' => 
        array (
        ),
        '泰和县' => 
        array (
        ),
        '遂川县' => 
        array (
        ),
        '万安县' => 
        array (
        ),
        '安福县' => 
        array (
        ),
        '永新县' => 
        array (
        ),
        '井冈山市' => 
        array (
        ),
      ),
      '宜春' => 
      array (
        '袁州区' => 
        array (
        ),
        '奉新县' => 
        array (
        ),
        '万载县' => 
        array (
        ),
        '上高县' => 
        array (
        ),
        '宜丰县' => 
        array (
        ),
        '靖安县' => 
        array (
        ),
        '铜鼓县' => 
        array (
        ),
        '丰城市' => 
        array (
        ),
        '樟树市' => 
        array (
        ),
        '高安市' => 
        array (
        ),
      ),
      '抚州' => 
      array (
        '临川区' => 
        array (
        ),
        '东乡区' => 
        array (
        ),
        '南城县' => 
        array (
        ),
        '黎川县' => 
        array (
        ),
        '南丰县' => 
        array (
        ),
        '崇仁县' => 
        array (
        ),
        '乐安县' => 
        array (
        ),
        '宜黄县' => 
        array (
        ),
        '金溪县' => 
        array (
        ),
        '资溪县' => 
        array (
        ),
        '广昌县' => 
        array (
        ),
      ),
      '上饶' => 
      array (
        '信州区' => 
        array (
        ),
        '广丰区' => 
        array (
        ),
        '广信区' => 
        array (
        ),
        '玉山县' => 
        array (
        ),
        '铅山县' => 
        array (
        ),
        '横峰县' => 
        array (
        ),
        '弋阳县' => 
        array (
        ),
        '余干县' => 
        array (
        ),
        '鄱阳县' => 
        array (
        ),
        '万年县' => 
        array (
        ),
        '婺源县' => 
        array (
        ),
        '德兴市' => 
        array (
        ),
      ),
    ),
    '山东' => 
    array (
      '济南' => 
      array (
        '历下区' => 
        array (
        ),
        '市中区' => 
        array (
        ),
        '槐荫区' => 
        array (
        ),
        '天桥区' => 
        array (
        ),
        '历城区' => 
        array (
        ),
        '长清区' => 
        array (
        ),
        '章丘区' => 
        array (
        ),
        '济阳区' => 
        array (
        ),
        '莱芜区' => 
        array (
        ),
        '钢城区' => 
        array (
        ),
        '平阴县' => 
        array (
        ),
        '商河县' => 
        array (
        ),
        '济南高新技术产业开发区' => 
        array (
        ),
      ),
      '青岛' => 
      array (
        '市南区' => 
        array (
        ),
        '市北区' => 
        array (
        ),
        '黄岛区' => 
        array (
        ),
        '崂山区' => 
        array (
        ),
        '李沧区' => 
        array (
        ),
        '城阳区' => 
        array (
        ),
        '即墨区' => 
        array (
        ),
        '胶州市' => 
        array (
        ),
        '平度市' => 
        array (
        ),
        '莱西市' => 
        array (
        ),
      ),
      '淄博' => 
      array (
        '淄川区' => 
        array (
        ),
        '张店区' => 
        array (
        ),
        '博山区' => 
        array (
        ),
        '临淄区' => 
        array (
        ),
        '周村区' => 
        array (
        ),
        '桓台县' => 
        array (
        ),
        '高青县' => 
        array (
        ),
        '沂源县' => 
        array (
        ),
      ),
      '枣庄' => 
      array (
        '市中区' => 
        array (
        ),
        '薛城区' => 
        array (
        ),
        '峄城区' => 
        array (
        ),
        '台儿庄区' => 
        array (
        ),
        '山亭区' => 
        array (
        ),
        '滕州市' => 
        array (
        ),
      ),
      '东营' => 
      array (
        '东营区' => 
        array (
        ),
        '河口区' => 
        array (
        ),
        '垦利区' => 
        array (
        ),
        '利津县' => 
        array (
        ),
        '广饶县' => 
        array (
        ),
        '东营经济技术开发区' => 
        array (
        ),
        '东营港经济开发区' => 
        array (
        ),
      ),
      '烟台' => 
      array (
        '芝罘区' => 
        array (
        ),
        '福山区' => 
        array (
        ),
        '牟平区' => 
        array (
        ),
        '莱山区' => 
        array (
        ),
        '蓬莱区' => 
        array (
        ),
        '烟台高新技术产业开发区' => 
        array (
        ),
        '烟台经济技术开发区' => 
        array (
        ),
        '龙口市' => 
        array (
        ),
        '莱阳市' => 
        array (
        ),
        '莱州市' => 
        array (
        ),
        '招远市' => 
        array (
        ),
        '栖霞市' => 
        array (
        ),
        '海阳市' => 
        array (
        ),
      ),
      '潍坊' => 
      array (
        '潍城区' => 
        array (
        ),
        '寒亭区' => 
        array (
        ),
        '坊子区' => 
        array (
        ),
        '奎文区' => 
        array (
        ),
        '临朐县' => 
        array (
        ),
        '昌乐县' => 
        array (
        ),
        '潍坊滨海经济技术开发区' => 
        array (
        ),
        '青州市' => 
        array (
        ),
        '诸城市' => 
        array (
        ),
        '寿光市' => 
        array (
        ),
        '安丘市' => 
        array (
        ),
        '高密市' => 
        array (
        ),
        '昌邑市' => 
        array (
        ),
      ),
      '济宁' => 
      array (
        '任城区' => 
        array (
        ),
        '兖州区' => 
        array (
        ),
        '微山县' => 
        array (
        ),
        '鱼台县' => 
        array (
        ),
        '金乡县' => 
        array (
        ),
        '嘉祥县' => 
        array (
        ),
        '汶上县' => 
        array (
        ),
        '泗水县' => 
        array (
        ),
        '梁山县' => 
        array (
        ),
        '济宁高新技术产业开发区' => 
        array (
        ),
        '曲阜市' => 
        array (
        ),
        '邹城市' => 
        array (
        ),
      ),
      '泰安' => 
      array (
        '泰山区' => 
        array (
        ),
        '岱岳区' => 
        array (
        ),
        '宁阳县' => 
        array (
        ),
        '东平县' => 
        array (
        ),
        '新泰市' => 
        array (
        ),
        '肥城市' => 
        array (
        ),
      ),
      '威海' => 
      array (
        '环翠区' => 
        array (
        ),
        '文登区' => 
        array (
        ),
        '威海火炬高技术产业开发区' => 
        array (
        ),
        '威海经济技术开发区' => 
        array (
        ),
        '威海临港经济技术开发区' => 
        array (
        ),
        '荣成市' => 
        array (
        ),
        '乳山市' => 
        array (
        ),
      ),
      '日照' => 
      array (
        '东港区' => 
        array (
        ),
        '岚山区' => 
        array (
        ),
        '五莲县' => 
        array (
        ),
        '莒县' => 
        array (
        ),
        '日照经济技术开发区' => 
        array (
        ),
      ),
      '临沂' => 
      array (
        '兰山区' => 
        array (
        ),
        '罗庄区' => 
        array (
        ),
        '河东区' => 
        array (
        ),
        '沂南县' => 
        array (
        ),
        '郯城县' => 
        array (
        ),
        '沂水县' => 
        array (
        ),
        '兰陵县' => 
        array (
        ),
        '费县' => 
        array (
        ),
        '平邑县' => 
        array (
        ),
        '莒南县' => 
        array (
        ),
        '蒙阴县' => 
        array (
        ),
        '临沭县' => 
        array (
        ),
        '临沂高新技术产业开发区' => 
        array (
        ),
      ),
      '德州' => 
      array (
        '德城区' => 
        array (
        ),
        '陵城区' => 
        array (
        ),
        '宁津县' => 
        array (
        ),
        '庆云县' => 
        array (
        ),
        '临邑县' => 
        array (
        ),
        '齐河县' => 
        array (
        ),
        '平原县' => 
        array (
        ),
        '夏津县' => 
        array (
        ),
        '武城县' => 
        array (
        ),
        '德州天衢新区' => 
        array (
        ),
        '乐陵市' => 
        array (
        ),
        '禹城市' => 
        array (
        ),
      ),
      '聊城' => 
      array (
        '东昌府区' => 
        array (
        ),
        '茌平区' => 
        array (
        ),
        '阳谷县' => 
        array (
        ),
        '莘县' => 
        array (
        ),
        '东阿县' => 
        array (
        ),
        '冠县' => 
        array (
        ),
        '高唐县' => 
        array (
        ),
        '临清市' => 
        array (
        ),
      ),
      '滨州' => 
      array (
        '滨城区' => 
        array (
        ),
        '沾化区' => 
        array (
        ),
        '惠民县' => 
        array (
        ),
        '阳信县' => 
        array (
        ),
        '无棣县' => 
        array (
        ),
        '博兴县' => 
        array (
        ),
        '邹平市' => 
        array (
        ),
      ),
      '菏泽' => 
      array (
        '牡丹区' => 
        array (
        ),
        '定陶区' => 
        array (
        ),
        '曹县' => 
        array (
        ),
        '单县' => 
        array (
        ),
        '成武县' => 
        array (
        ),
        '巨野县' => 
        array (
        ),
        '郓城县' => 
        array (
        ),
        '鄄城县' => 
        array (
        ),
        '东明县' => 
        array (
        ),
        '菏泽经济技术开发区' => 
        array (
        ),
        '菏泽高新技术开发区' => 
        array (
        ),
      ),
    ),
    '河南' => 
    array (
      '郑州' => 
      array (
        '中原区' => 
        array (
        ),
        '二七区' => 
        array (
        ),
        '管城回族区' => 
        array (
        ),
        '金水区' => 
        array (
        ),
        '上街区' => 
        array (
        ),
        '惠济区' => 
        array (
        ),
        '中牟县' => 
        array (
        ),
        '郑州经济技术开发区' => 
        array (
        ),
        '郑州高新技术产业开发区' => 
        array (
        ),
        '郑州航空港经济综合实验区' => 
        array (
        ),
        '巩义市' => 
        array (
        ),
        '荥阳市' => 
        array (
        ),
        '新密市' => 
        array (
        ),
        '新郑市' => 
        array (
        ),
        '登封市' => 
        array (
        ),
      ),
      '开封' => 
      array (
        '龙亭区' => 
        array (
        ),
        '顺河回族区' => 
        array (
        ),
        '鼓楼区' => 
        array (
        ),
        '禹王台区' => 
        array (
        ),
        '祥符区' => 
        array (
        ),
        '杞县' => 
        array (
        ),
        '通许县' => 
        array (
        ),
        '尉氏县' => 
        array (
        ),
        '兰考县' => 
        array (
        ),
      ),
      '洛阳' => 
      array (
        '老城区' => 
        array (
        ),
        '西工区' => 
        array (
        ),
        '瀍河回族区' => 
        array (
        ),
        '涧西区' => 
        array (
        ),
        '偃师区' => 
        array (
        ),
        '孟津区' => 
        array (
        ),
        '洛龙区' => 
        array (
        ),
        '新安县' => 
        array (
        ),
        '栾川县' => 
        array (
        ),
        '嵩县' => 
        array (
        ),
        '汝阳县' => 
        array (
        ),
        '宜阳县' => 
        array (
        ),
        '洛宁县' => 
        array (
        ),
        '伊川县' => 
        array (
        ),
        '洛阳高新技术产业开发区' => 
        array (
        ),
      ),
      '平顶山' => 
      array (
        '新华区' => 
        array (
        ),
        '卫东区' => 
        array (
        ),
        '石龙区' => 
        array (
        ),
        '湛河区' => 
        array (
        ),
        '宝丰县' => 
        array (
        ),
        '叶县' => 
        array (
        ),
        '鲁山县' => 
        array (
        ),
        '郏县' => 
        array (
        ),
        '平顶山高新技术产业开发区' => 
        array (
        ),
        '平顶山市城乡一体化示范区' => 
        array (
        ),
        '舞钢市' => 
        array (
        ),
        '汝州市' => 
        array (
        ),
      ),
      '安阳' => 
      array (
        '文峰区' => 
        array (
        ),
        '北关区' => 
        array (
        ),
        '殷都区' => 
        array (
        ),
        '龙安区' => 
        array (
        ),
        '安阳县' => 
        array (
        ),
        '汤阴县' => 
        array (
        ),
        '滑县' => 
        array (
        ),
        '内黄县' => 
        array (
        ),
        '安阳高新技术产业开发区' => 
        array (
        ),
        '林州市' => 
        array (
        ),
      ),
      '鹤壁' => 
      array (
        '鹤山区' => 
        array (
        ),
        '山城区' => 
        array (
        ),
        '淇滨区' => 
        array (
        ),
        '浚县' => 
        array (
        ),
        '淇县' => 
        array (
        ),
        '鹤壁经济技术开发区' => 
        array (
        ),
      ),
      '新乡' => 
      array (
        '红旗区' => 
        array (
        ),
        '卫滨区' => 
        array (
        ),
        '凤泉区' => 
        array (
        ),
        '牧野区' => 
        array (
        ),
        '新乡县' => 
        array (
        ),
        '获嘉县' => 
        array (
        ),
        '原阳县' => 
        array (
        ),
        '延津县' => 
        array (
        ),
        '封丘县' => 
        array (
        ),
        '新乡高新技术产业开发区' => 
        array (
        ),
        '新乡经济技术开发区' => 
        array (
        ),
        '新乡市平原城乡一体化示范区' => 
        array (
        ),
        '卫辉市' => 
        array (
        ),
        '辉县市' => 
        array (
        ),
        '长垣市' => 
        array (
        ),
      ),
      '焦作' => 
      array (
        '解放区' => 
        array (
        ),
        '中站区' => 
        array (
        ),
        '马村区' => 
        array (
        ),
        '山阳区' => 
        array (
        ),
        '修武县' => 
        array (
        ),
        '博爱县' => 
        array (
        ),
        '武陟县' => 
        array (
        ),
        '温县' => 
        array (
        ),
        '焦作城乡一体化示范区' => 
        array (
        ),
        '沁阳市' => 
        array (
        ),
        '孟州市' => 
        array (
        ),
      ),
      '濮阳' => 
      array (
        '华龙区' => 
        array (
        ),
        '清丰县' => 
        array (
        ),
        '南乐县' => 
        array (
        ),
        '范县' => 
        array (
        ),
        '台前县' => 
        array (
        ),
        '濮阳县' => 
        array (
        ),
        '河南濮阳工业园区' => 
        array (
        ),
        '濮阳经济技术开发区' => 
        array (
        ),
      ),
      '许昌' => 
      array (
        '魏都区' => 
        array (
        ),
        '建安区' => 
        array (
        ),
        '鄢陵县' => 
        array (
        ),
        '襄城县' => 
        array (
        ),
        '许昌经济技术开发区' => 
        array (
        ),
        '禹州市' => 
        array (
        ),
        '长葛市' => 
        array (
        ),
      ),
      '漯河' => 
      array (
        '源汇区' => 
        array (
        ),
        '郾城区' => 
        array (
        ),
        '召陵区' => 
        array (
        ),
        '舞阳县' => 
        array (
        ),
        '临颍县' => 
        array (
        ),
        '漯河经济技术开发区' => 
        array (
        ),
      ),
      '三门峡' => 
      array (
        '湖滨区' => 
        array (
        ),
        '陕州区' => 
        array (
        ),
        '渑池县' => 
        array (
        ),
        '卢氏县' => 
        array (
        ),
        '河南三门峡经济开发区' => 
        array (
        ),
        '义马市' => 
        array (
        ),
        '灵宝市' => 
        array (
        ),
      ),
      '南阳' => 
      array (
        '宛城区' => 
        array (
        ),
        '卧龙区' => 
        array (
        ),
        '南召县' => 
        array (
        ),
        '方城县' => 
        array (
        ),
        '西峡县' => 
        array (
        ),
        '镇平县' => 
        array (
        ),
        '内乡县' => 
        array (
        ),
        '淅川县' => 
        array (
        ),
        '社旗县' => 
        array (
        ),
        '唐河县' => 
        array (
        ),
        '新野县' => 
        array (
        ),
        '桐柏县' => 
        array (
        ),
        '南阳高新技术产业开发区' => 
        array (
        ),
        '南阳市城乡一体化示范区' => 
        array (
        ),
        '邓州市' => 
        array (
        ),
      ),
      '商丘' => 
      array (
        '梁园区' => 
        array (
        ),
        '睢阳区' => 
        array (
        ),
        '民权县' => 
        array (
        ),
        '睢县' => 
        array (
        ),
        '宁陵县' => 
        array (
        ),
        '柘城县' => 
        array (
        ),
        '虞城县' => 
        array (
        ),
        '夏邑县' => 
        array (
        ),
        '豫东综合物流产业聚集区' => 
        array (
        ),
        '河南商丘经济开发区' => 
        array (
        ),
        '永城市' => 
        array (
        ),
      ),
      '信阳' => 
      array (
        '浉河区' => 
        array (
        ),
        '平桥区' => 
        array (
        ),
        '罗山县' => 
        array (
        ),
        '光山县' => 
        array (
        ),
        '新县' => 
        array (
        ),
        '商城县' => 
        array (
        ),
        '固始县' => 
        array (
        ),
        '潢川县' => 
        array (
        ),
        '淮滨县' => 
        array (
        ),
        '息县' => 
        array (
        ),
        '信阳高新技术产业开发区' => 
        array (
        ),
      ),
      '周口' => 
      array (
        '川汇区' => 
        array (
        ),
        '淮阳区' => 
        array (
        ),
        '扶沟县' => 
        array (
        ),
        '西华县' => 
        array (
        ),
        '商水县' => 
        array (
        ),
        '沈丘县' => 
        array (
        ),
        '郸城县' => 
        array (
        ),
        '太康县' => 
        array (
        ),
        '鹿邑县' => 
        array (
        ),
        '周口临港开发区' => 
        array (
        ),
        '项城市' => 
        array (
        ),
      ),
      '驻马店' => 
      array (
        '驿城区' => 
        array (
        ),
        '西平县' => 
        array (
        ),
        '上蔡县' => 
        array (
        ),
        '平舆县' => 
        array (
        ),
        '正阳县' => 
        array (
        ),
        '确山县' => 
        array (
        ),
        '泌阳县' => 
        array (
        ),
        '汝南县' => 
        array (
        ),
        '遂平县' => 
        array (
        ),
        '新蔡县' => 
        array (
        ),
        '河南驻马店经济开发区' => 
        array (
        ),
      ),
      '省直辖县级行政区划' => 
      array (
        '济源市' => 
        array (
        ),
      ),
    ),
    '湖北' => 
    array (
      '武汉' => 
      array (
        '江岸区' => 
        array (
        ),
        '江汉区' => 
        array (
        ),
        '硚口区' => 
        array (
        ),
        '汉阳区' => 
        array (
        ),
        '武昌区' => 
        array (
        ),
        '青山区' => 
        array (
        ),
        '洪山区' => 
        array (
        ),
        '东西湖区' => 
        array (
        ),
        '汉南区' => 
        array (
        ),
        '蔡甸区' => 
        array (
        ),
        '江夏区' => 
        array (
        ),
        '黄陂区' => 
        array (
        ),
        '新洲区' => 
        array (
        ),
      ),
      '黄石' => 
      array (
        '黄石港区' => 
        array (
        ),
        '西塞山区' => 
        array (
        ),
        '下陆区' => 
        array (
        ),
        '铁山区' => 
        array (
        ),
        '阳新县' => 
        array (
        ),
        '大冶市' => 
        array (
        ),
      ),
      '十堰' => 
      array (
        '茅箭区' => 
        array (
        ),
        '张湾区' => 
        array (
        ),
        '郧阳区' => 
        array (
        ),
        '郧西县' => 
        array (
        ),
        '竹山县' => 
        array (
        ),
        '竹溪县' => 
        array (
        ),
        '房县' => 
        array (
        ),
        '丹江口市' => 
        array (
        ),
      ),
      '宜昌' => 
      array (
        '西陵区' => 
        array (
        ),
        '伍家岗区' => 
        array (
        ),
        '点军区' => 
        array (
        ),
        '猇亭区' => 
        array (
        ),
        '夷陵区' => 
        array (
        ),
        '远安县' => 
        array (
        ),
        '兴山县' => 
        array (
        ),
        '秭归县' => 
        array (
        ),
        '长阳土家族自治县' => 
        array (
        ),
        '五峰土家族自治县' => 
        array (
        ),
        '宜都市' => 
        array (
        ),
        '当阳市' => 
        array (
        ),
        '枝江市' => 
        array (
        ),
      ),
      '襄阳' => 
      array (
        '襄城区' => 
        array (
        ),
        '樊城区' => 
        array (
        ),
        '襄州区' => 
        array (
        ),
        '南漳县' => 
        array (
        ),
        '谷城县' => 
        array (
        ),
        '保康县' => 
        array (
        ),
        '老河口市' => 
        array (
        ),
        '枣阳市' => 
        array (
        ),
        '宜城市' => 
        array (
        ),
      ),
      '鄂州' => 
      array (
        '梁子湖区' => 
        array (
        ),
        '华容区' => 
        array (
        ),
        '鄂城区' => 
        array (
        ),
      ),
      '荆门' => 
      array (
        '东宝区' => 
        array (
        ),
        '掇刀区' => 
        array (
        ),
        '沙洋县' => 
        array (
        ),
        '钟祥市' => 
        array (
        ),
        '京山市' => 
        array (
        ),
      ),
      '孝感' => 
      array (
        '孝南区' => 
        array (
        ),
        '孝昌县' => 
        array (
        ),
        '大悟县' => 
        array (
        ),
        '云梦县' => 
        array (
        ),
        '应城市' => 
        array (
        ),
        '安陆市' => 
        array (
        ),
        '汉川市' => 
        array (
        ),
      ),
      '荆州' => 
      array (
        '沙市区' => 
        array (
        ),
        '荆州区' => 
        array (
        ),
        '公安县' => 
        array (
        ),
        '江陵县' => 
        array (
        ),
        '荆州经济技术开发区' => 
        array (
        ),
        '石首市' => 
        array (
        ),
        '洪湖市' => 
        array (
        ),
        '松滋市' => 
        array (
        ),
        '监利市' => 
        array (
        ),
      ),
      '黄冈' => 
      array (
        '黄州区' => 
        array (
        ),
        '团风县' => 
        array (
        ),
        '红安县' => 
        array (
        ),
        '罗田县' => 
        array (
        ),
        '英山县' => 
        array (
        ),
        '浠水县' => 
        array (
        ),
        '蕲春县' => 
        array (
        ),
        '黄梅县' => 
        array (
        ),
        '龙感湖管理区' => 
        array (
        ),
        '麻城市' => 
        array (
        ),
        '武穴市' => 
        array (
        ),
      ),
      '咸宁' => 
      array (
        '咸安区' => 
        array (
        ),
        '嘉鱼县' => 
        array (
        ),
        '通城县' => 
        array (
        ),
        '崇阳县' => 
        array (
        ),
        '通山县' => 
        array (
        ),
        '赤壁市' => 
        array (
        ),
      ),
      '随州' => 
      array (
        '曾都区' => 
        array (
        ),
        '随县' => 
        array (
        ),
        '广水市' => 
        array (
        ),
      ),
      '恩施土家族苗族自治州' => 
      array (
        '恩施市' => 
        array (
        ),
        '利川市' => 
        array (
        ),
        '建始县' => 
        array (
        ),
        '巴东县' => 
        array (
        ),
        '宣恩县' => 
        array (
        ),
        '咸丰县' => 
        array (
        ),
        '来凤县' => 
        array (
        ),
        '鹤峰县' => 
        array (
        ),
      ),
      '省直辖县级行政区划' => 
      array (
        '仙桃市' => 
        array (
        ),
        '潜江市' => 
        array (
        ),
        '天门市' => 
        array (
        ),
        '神农架林区' => 
        array (
        ),
      ),
    ),
    '湖南' => 
    array (
      '长沙' => 
      array (
        '芙蓉区' => 
        array (
        ),
        '天心区' => 
        array (
        ),
        '岳麓区' => 
        array (
        ),
        '开福区' => 
        array (
        ),
        '雨花区' => 
        array (
        ),
        '望城区' => 
        array (
        ),
        '长沙县' => 
        array (
        ),
        '浏阳市' => 
        array (
        ),
        '宁乡市' => 
        array (
        ),
      ),
      '株洲' => 
      array (
        '荷塘区' => 
        array (
        ),
        '芦淞区' => 
        array (
        ),
        '石峰区' => 
        array (
        ),
        '天元区' => 
        array (
        ),
        '渌口区' => 
        array (
        ),
        '攸县' => 
        array (
        ),
        '茶陵县' => 
        array (
        ),
        '炎陵县' => 
        array (
        ),
        '醴陵市' => 
        array (
        ),
      ),
      '湘潭' => 
      array (
        '雨湖区' => 
        array (
        ),
        '岳塘区' => 
        array (
        ),
        '湘潭县' => 
        array (
        ),
        '湖南湘潭高新技术产业园区' => 
        array (
        ),
        '湘潭昭山示范区' => 
        array (
        ),
        '湘潭九华示范区' => 
        array (
        ),
        '湘乡市' => 
        array (
        ),
        '韶山市' => 
        array (
        ),
      ),
      '衡阳' => 
      array (
        '珠晖区' => 
        array (
        ),
        '雁峰区' => 
        array (
        ),
        '石鼓区' => 
        array (
        ),
        '蒸湘区' => 
        array (
        ),
        '南岳区' => 
        array (
        ),
        '衡阳县' => 
        array (
        ),
        '衡南县' => 
        array (
        ),
        '衡山县' => 
        array (
        ),
        '衡东县' => 
        array (
        ),
        '祁东县' => 
        array (
        ),
        '湖南衡阳松木经济开发区' => 
        array (
        ),
        '湖南衡阳高新技术产业园区' => 
        array (
        ),
        '耒阳市' => 
        array (
        ),
        '常宁市' => 
        array (
        ),
      ),
      '邵阳' => 
      array (
        '双清区' => 
        array (
        ),
        '大祥区' => 
        array (
        ),
        '北塔区' => 
        array (
        ),
        '新邵县' => 
        array (
        ),
        '邵阳县' => 
        array (
        ),
        '隆回县' => 
        array (
        ),
        '洞口县' => 
        array (
        ),
        '绥宁县' => 
        array (
        ),
        '新宁县' => 
        array (
        ),
        '城步苗族自治县' => 
        array (
        ),
        '武冈市' => 
        array (
        ),
        '邵东市' => 
        array (
        ),
      ),
      '岳阳' => 
      array (
        '岳阳楼区' => 
        array (
        ),
        '云溪区' => 
        array (
        ),
        '君山区' => 
        array (
        ),
        '岳阳县' => 
        array (
        ),
        '华容县' => 
        array (
        ),
        '湘阴县' => 
        array (
        ),
        '平江县' => 
        array (
        ),
        '岳阳市屈原管理区' => 
        array (
        ),
        '汨罗市' => 
        array (
        ),
        '临湘市' => 
        array (
        ),
      ),
      '常德' => 
      array (
        '武陵区' => 
        array (
        ),
        '鼎城区' => 
        array (
        ),
        '安乡县' => 
        array (
        ),
        '汉寿县' => 
        array (
        ),
        '澧县' => 
        array (
        ),
        '临澧县' => 
        array (
        ),
        '桃源县' => 
        array (
        ),
        '石门县' => 
        array (
        ),
        '常德市西洞庭管理区' => 
        array (
        ),
        '津市市' => 
        array (
        ),
      ),
      '张家界' => 
      array (
        '永定区' => 
        array (
        ),
        '武陵源区' => 
        array (
        ),
        '慈利县' => 
        array (
        ),
        '桑植县' => 
        array (
        ),
      ),
      '益阳' => 
      array (
        '资阳区' => 
        array (
        ),
        '赫山区' => 
        array (
        ),
        '南县' => 
        array (
        ),
        '桃江县' => 
        array (
        ),
        '安化县' => 
        array (
        ),
        '益阳市大通湖管理区' => 
        array (
        ),
        '湖南益阳高新技术产业园区' => 
        array (
        ),
        '沅江市' => 
        array (
        ),
      ),
      '郴州' => 
      array (
        '北湖区' => 
        array (
        ),
        '苏仙区' => 
        array (
        ),
        '桂阳县' => 
        array (
        ),
        '宜章县' => 
        array (
        ),
        '永兴县' => 
        array (
        ),
        '嘉禾县' => 
        array (
        ),
        '临武县' => 
        array (
        ),
        '汝城县' => 
        array (
        ),
        '桂东县' => 
        array (
        ),
        '安仁县' => 
        array (
        ),
        '资兴市' => 
        array (
        ),
      ),
      '永州' => 
      array (
        '零陵区' => 
        array (
        ),
        '冷水滩区' => 
        array (
        ),
        '东安县' => 
        array (
        ),
        '双牌县' => 
        array (
        ),
        '道县' => 
        array (
        ),
        '江永县' => 
        array (
        ),
        '宁远县' => 
        array (
        ),
        '蓝山县' => 
        array (
        ),
        '新田县' => 
        array (
        ),
        '江华瑶族自治县' => 
        array (
        ),
        '永州经济技术开发区' => 
        array (
        ),
        '永州市回龙圩管理区' => 
        array (
        ),
        '祁阳市' => 
        array (
        ),
      ),
      '怀化' => 
      array (
        '鹤城区' => 
        array (
        ),
        '中方县' => 
        array (
        ),
        '沅陵县' => 
        array (
        ),
        '辰溪县' => 
        array (
        ),
        '溆浦县' => 
        array (
        ),
        '会同县' => 
        array (
        ),
        '麻阳苗族自治县' => 
        array (
        ),
        '新晃侗族自治县' => 
        array (
        ),
        '芷江侗族自治县' => 
        array (
        ),
        '靖州苗族侗族自治县' => 
        array (
        ),
        '通道侗族自治县' => 
        array (
        ),
        '怀化市洪江管理区' => 
        array (
        ),
        '洪江市' => 
        array (
        ),
      ),
      '娄底' => 
      array (
        '娄星区' => 
        array (
        ),
        '双峰县' => 
        array (
        ),
        '新化县' => 
        array (
        ),
        '冷水江市' => 
        array (
        ),
        '涟源市' => 
        array (
        ),
      ),
      '湘西土家族苗族自治州' => 
      array (
        '吉首市' => 
        array (
        ),
        '泸溪县' => 
        array (
        ),
        '凤凰县' => 
        array (
        ),
        '花垣县' => 
        array (
        ),
        '保靖县' => 
        array (
        ),
        '古丈县' => 
        array (
        ),
        '永顺县' => 
        array (
        ),
        '龙山县' => 
        array (
        ),
      ),
    ),
    '广东' => 
    array (
      '广州' => 
      array (
        '荔湾区' => 
        array (
        ),
        '越秀区' => 
        array (
        ),
        '海珠区' => 
        array (
        ),
        '天河区' => 
        array (
        ),
        '白云区' => 
        array (
        ),
        '黄埔区' => 
        array (
        ),
        '番禺区' => 
        array (
        ),
        '花都区' => 
        array (
        ),
        '南沙区' => 
        array (
        ),
        '从化区' => 
        array (
        ),
        '增城区' => 
        array (
        ),
      ),
      '韶关' => 
      array (
        '武江区' => 
        array (
        ),
        '浈江区' => 
        array (
        ),
        '曲江区' => 
        array (
        ),
        '始兴县' => 
        array (
        ),
        '仁化县' => 
        array (
        ),
        '翁源县' => 
        array (
        ),
        '乳源瑶族自治县' => 
        array (
        ),
        '新丰县' => 
        array (
        ),
        '乐昌市' => 
        array (
        ),
        '南雄市' => 
        array (
        ),
      ),
      '深圳' => 
      array (
        '罗湖区' => 
        array (
        ),
        '福田区' => 
        array (
        ),
        '南山区' => 
        array (
        ),
        '宝安区' => 
        array (
        ),
        '龙岗区' => 
        array (
        ),
        '盐田区' => 
        array (
        ),
        '龙华区' => 
        array (
        ),
        '坪山区' => 
        array (
        ),
        '光明区' => 
        array (
        ),
      ),
      '珠海' => 
      array (
        '香洲区' => 
        array (
        ),
        '斗门区' => 
        array (
        ),
        '金湾区' => 
        array (
        ),
      ),
      '汕头' => 
      array (
        '龙湖区' => 
        array (
        ),
        '金平区' => 
        array (
        ),
        '濠江区' => 
        array (
        ),
        '潮阳区' => 
        array (
        ),
        '潮南区' => 
        array (
        ),
        '澄海区' => 
        array (
        ),
        '南澳县' => 
        array (
        ),
      ),
      '佛山' => 
      array (
        '禅城区' => 
        array (
        ),
        '南海区' => 
        array (
        ),
        '顺德区' => 
        array (
        ),
        '三水区' => 
        array (
        ),
        '高明区' => 
        array (
        ),
      ),
      '江门' => 
      array (
        '蓬江区' => 
        array (
        ),
        '江海区' => 
        array (
        ),
        '新会区' => 
        array (
        ),
        '台山市' => 
        array (
        ),
        '开平市' => 
        array (
        ),
        '鹤山市' => 
        array (
        ),
        '恩平市' => 
        array (
        ),
      ),
      '湛江' => 
      array (
        '赤坎区' => 
        array (
        ),
        '霞山区' => 
        array (
        ),
        '坡头区' => 
        array (
        ),
        '麻章区' => 
        array (
        ),
        '遂溪县' => 
        array (
        ),
        '徐闻县' => 
        array (
        ),
        '廉江市' => 
        array (
        ),
        '雷州市' => 
        array (
        ),
        '吴川市' => 
        array (
        ),
      ),
      '茂名' => 
      array (
        '茂南区' => 
        array (
        ),
        '电白区' => 
        array (
        ),
        '高州市' => 
        array (
        ),
        '化州市' => 
        array (
        ),
        '信宜市' => 
        array (
        ),
      ),
      '肇庆' => 
      array (
        '端州区' => 
        array (
        ),
        '鼎湖区' => 
        array (
        ),
        '高要区' => 
        array (
        ),
        '广宁县' => 
        array (
        ),
        '怀集县' => 
        array (
        ),
        '封开县' => 
        array (
        ),
        '德庆县' => 
        array (
        ),
        '四会市' => 
        array (
        ),
      ),
      '惠州' => 
      array (
        '惠城区' => 
        array (
        ),
        '惠阳区' => 
        array (
        ),
        '博罗县' => 
        array (
        ),
        '惠东县' => 
        array (
        ),
        '龙门县' => 
        array (
        ),
      ),
      '梅州' => 
      array (
        '梅江区' => 
        array (
        ),
        '梅县区' => 
        array (
        ),
        '大埔县' => 
        array (
        ),
        '丰顺县' => 
        array (
        ),
        '五华县' => 
        array (
        ),
        '平远县' => 
        array (
        ),
        '蕉岭县' => 
        array (
        ),
        '兴宁市' => 
        array (
        ),
      ),
      '汕尾' => 
      array (
        '城区' => 
        array (
        ),
        '海丰县' => 
        array (
        ),
        '陆河县' => 
        array (
        ),
        '陆丰市' => 
        array (
        ),
      ),
      '河源' => 
      array (
        '源城区' => 
        array (
        ),
        '紫金县' => 
        array (
        ),
        '龙川县' => 
        array (
        ),
        '连平县' => 
        array (
        ),
        '和平县' => 
        array (
        ),
        '东源县' => 
        array (
        ),
      ),
      '阳江' => 
      array (
        '江城区' => 
        array (
        ),
        '阳东区' => 
        array (
        ),
        '阳西县' => 
        array (
        ),
        '阳春市' => 
        array (
        ),
      ),
      '清远' => 
      array (
        '清城区' => 
        array (
        ),
        '清新区' => 
        array (
        ),
        '佛冈县' => 
        array (
        ),
        '阳山县' => 
        array (
        ),
        '连山壮族瑶族自治县' => 
        array (
        ),
        '连南瑶族自治县' => 
        array (
        ),
        '英德市' => 
        array (
        ),
        '连州市' => 
        array (
        ),
      ),
      '东莞' => 
      array (
        '东城街道' => 
        array (
        ),
        '南城街道' => 
        array (
        ),
        '万江街道' => 
        array (
        ),
        '莞城街道' => 
        array (
        ),
        '石碣镇' => 
        array (
        ),
        '石龙镇' => 
        array (
        ),
        '茶山镇' => 
        array (
        ),
        '石排镇' => 
        array (
        ),
        '企石镇' => 
        array (
        ),
        '横沥镇' => 
        array (
        ),
        '桥头镇' => 
        array (
        ),
        '谢岗镇' => 
        array (
        ),
        '东坑镇' => 
        array (
        ),
        '常平镇' => 
        array (
        ),
        '寮步镇' => 
        array (
        ),
        '樟木头镇' => 
        array (
        ),
        '大朗镇' => 
        array (
        ),
        '黄江镇' => 
        array (
        ),
        '清溪镇' => 
        array (
        ),
        '塘厦镇' => 
        array (
        ),
        '凤岗镇' => 
        array (
        ),
        '大岭山镇' => 
        array (
        ),
        '长安镇' => 
        array (
        ),
        '虎门镇' => 
        array (
        ),
        '厚街镇' => 
        array (
        ),
        '沙田镇' => 
        array (
        ),
        '道滘镇' => 
        array (
        ),
        '洪梅镇' => 
        array (
        ),
        '麻涌镇' => 
        array (
        ),
        '望牛墩镇' => 
        array (
        ),
        '中堂镇' => 
        array (
        ),
        '高埗镇' => 
        array (
        ),
        '松山湖' => 
        array (
        ),
        '东莞港' => 
        array (
        ),
        '东莞生态园' => 
        array (
        ),
        '东莞滨海湾新区' => 
        array (
        ),
      ),
      '中山' => 
      array (
        '石岐街道' => 
        array (
        ),
        '东区街道' => 
        array (
        ),
        '中山港街道' => 
        array (
        ),
        '西区街道' => 
        array (
        ),
        '南区街道' => 
        array (
        ),
        '五桂山街道' => 
        array (
        ),
        '民众街道' => 
        array (
        ),
        '南朗街道' => 
        array (
        ),
        '黄圃镇' => 
        array (
        ),
        '东凤镇' => 
        array (
        ),
        '古镇镇' => 
        array (
        ),
        '沙溪镇' => 
        array (
        ),
        '坦洲镇' => 
        array (
        ),
        '港口镇' => 
        array (
        ),
        '三角镇' => 
        array (
        ),
        '横栏镇' => 
        array (
        ),
        '南头镇' => 
        array (
        ),
        '阜沙镇' => 
        array (
        ),
        '三乡镇' => 
        array (
        ),
        '板芙镇' => 
        array (
        ),
        '大涌镇' => 
        array (
        ),
        '神湾镇' => 
        array (
        ),
        '小榄镇' => 
        array (
        ),
      ),
      '潮州' => 
      array (
        '湘桥区' => 
        array (
        ),
        '潮安区' => 
        array (
        ),
        '饶平县' => 
        array (
        ),
      ),
      '揭阳' => 
      array (
        '榕城区' => 
        array (
        ),
        '揭东区' => 
        array (
        ),
        '揭西县' => 
        array (
        ),
        '惠来县' => 
        array (
        ),
        '普宁市' => 
        array (
        ),
      ),
      '云浮' => 
      array (
        '云城区' => 
        array (
        ),
        '云安区' => 
        array (
        ),
        '新兴县' => 
        array (
        ),
        '郁南县' => 
        array (
        ),
        '罗定市' => 
        array (
        ),
      ),
    ),
    '广西' => 
    array (
      '南宁' => 
      array (
        '兴宁区' => 
        array (
        ),
        '青秀区' => 
        array (
        ),
        '江南区' => 
        array (
        ),
        '西乡塘区' => 
        array (
        ),
        '良庆区' => 
        array (
        ),
        '邕宁区' => 
        array (
        ),
        '武鸣区' => 
        array (
        ),
        '隆安县' => 
        array (
        ),
        '马山县' => 
        array (
        ),
        '上林县' => 
        array (
        ),
        '宾阳县' => 
        array (
        ),
        '横州市' => 
        array (
        ),
      ),
      '柳州' => 
      array (
        '城中区' => 
        array (
        ),
        '鱼峰区' => 
        array (
        ),
        '柳南区' => 
        array (
        ),
        '柳北区' => 
        array (
        ),
        '柳江区' => 
        array (
        ),
        '柳城县' => 
        array (
        ),
        '鹿寨县' => 
        array (
        ),
        '融安县' => 
        array (
        ),
        '融水苗族自治县' => 
        array (
        ),
        '三江侗族自治县' => 
        array (
        ),
      ),
      '桂林' => 
      array (
        '秀峰区' => 
        array (
        ),
        '叠彩区' => 
        array (
        ),
        '象山区' => 
        array (
        ),
        '七星区' => 
        array (
        ),
        '雁山区' => 
        array (
        ),
        '临桂区' => 
        array (
        ),
        '阳朔县' => 
        array (
        ),
        '灵川县' => 
        array (
        ),
        '全州县' => 
        array (
        ),
        '兴安县' => 
        array (
        ),
        '永福县' => 
        array (
        ),
        '灌阳县' => 
        array (
        ),
        '龙胜各族自治县' => 
        array (
        ),
        '资源县' => 
        array (
        ),
        '平乐县' => 
        array (
        ),
        '恭城瑶族自治县' => 
        array (
        ),
        '荔浦市' => 
        array (
        ),
      ),
      '梧州' => 
      array (
        '万秀区' => 
        array (
        ),
        '长洲区' => 
        array (
        ),
        '龙圩区' => 
        array (
        ),
        '苍梧县' => 
        array (
        ),
        '藤县' => 
        array (
        ),
        '蒙山县' => 
        array (
        ),
        '岑溪市' => 
        array (
        ),
      ),
      '北海' => 
      array (
        '海城区' => 
        array (
        ),
        '银海区' => 
        array (
        ),
        '铁山港区' => 
        array (
        ),
        '合浦县' => 
        array (
        ),
      ),
      '防城港' => 
      array (
        '港口区' => 
        array (
        ),
        '防城区' => 
        array (
        ),
        '上思县' => 
        array (
        ),
        '东兴市' => 
        array (
        ),
      ),
      '钦州' => 
      array (
        '钦南区' => 
        array (
        ),
        '钦北区' => 
        array (
        ),
        '灵山县' => 
        array (
        ),
        '浦北县' => 
        array (
        ),
      ),
      '贵港' => 
      array (
        '港北区' => 
        array (
        ),
        '港南区' => 
        array (
        ),
        '覃塘区' => 
        array (
        ),
        '平南县' => 
        array (
        ),
        '桂平市' => 
        array (
        ),
      ),
      '玉林' => 
      array (
        '玉州区' => 
        array (
        ),
        '福绵区' => 
        array (
        ),
        '容县' => 
        array (
        ),
        '陆川县' => 
        array (
        ),
        '博白县' => 
        array (
        ),
        '兴业县' => 
        array (
        ),
        '北流市' => 
        array (
        ),
      ),
      '百色' => 
      array (
        '右江区' => 
        array (
        ),
        '田阳区' => 
        array (
        ),
        '田东县' => 
        array (
        ),
        '德保县' => 
        array (
        ),
        '那坡县' => 
        array (
        ),
        '凌云县' => 
        array (
        ),
        '乐业县' => 
        array (
        ),
        '田林县' => 
        array (
        ),
        '西林县' => 
        array (
        ),
        '隆林各族自治县' => 
        array (
        ),
        '靖西市' => 
        array (
        ),
        '平果市' => 
        array (
        ),
      ),
      '贺州' => 
      array (
        '八步区' => 
        array (
        ),
        '平桂区' => 
        array (
        ),
        '昭平县' => 
        array (
        ),
        '钟山县' => 
        array (
        ),
        '富川瑶族自治县' => 
        array (
        ),
      ),
      '河池' => 
      array (
        '金城江区' => 
        array (
        ),
        '宜州区' => 
        array (
        ),
        '南丹县' => 
        array (
        ),
        '天峨县' => 
        array (
        ),
        '凤山县' => 
        array (
        ),
        '东兰县' => 
        array (
        ),
        '罗城仫佬族自治县' => 
        array (
        ),
        '环江毛南族自治县' => 
        array (
        ),
        '巴马瑶族自治县' => 
        array (
        ),
        '都安瑶族自治县' => 
        array (
        ),
        '大化瑶族自治县' => 
        array (
        ),
      ),
      '来宾' => 
      array (
        '兴宾区' => 
        array (
        ),
        '忻城县' => 
        array (
        ),
        '象州县' => 
        array (
        ),
        '武宣县' => 
        array (
        ),
        '金秀瑶族自治县' => 
        array (
        ),
        '合山市' => 
        array (
        ),
      ),
      '崇左' => 
      array (
        '江州区' => 
        array (
        ),
        '扶绥县' => 
        array (
        ),
        '宁明县' => 
        array (
        ),
        '龙州县' => 
        array (
        ),
        '大新县' => 
        array (
        ),
        '天等县' => 
        array (
        ),
        '凭祥市' => 
        array (
        ),
      ),
    ),
    '海南' => 
    array (
      '海口' => 
      array (
        '秀英区' => 
        array (
        ),
        '龙华区' => 
        array (
        ),
        '琼山区' => 
        array (
        ),
        '美兰区' => 
        array (
        ),
      ),
      '三亚' => 
      array (
        '海棠区' => 
        array (
        ),
        '吉阳区' => 
        array (
        ),
        '天涯区' => 
        array (
        ),
        '崖州区' => 
        array (
        ),
      ),
      '三沙' => 
      array (
        '西沙群岛' => 
        array (
        ),
        '南沙群岛' => 
        array (
        ),
        '中沙群岛的岛礁及其海域' => 
        array (
        ),
      ),
      '儋州' => 
      array (
        '那大镇' => 
        array (
        ),
        '和庆镇' => 
        array (
        ),
        '南丰镇' => 
        array (
        ),
        '大成镇' => 
        array (
        ),
        '雅星镇' => 
        array (
        ),
        '兰洋镇' => 
        array (
        ),
        '光村镇' => 
        array (
        ),
        '木棠镇' => 
        array (
        ),
        '海头镇' => 
        array (
        ),
        '峨蔓镇' => 
        array (
        ),
        '王五镇' => 
        array (
        ),
        '白马井镇' => 
        array (
        ),
        '中和镇' => 
        array (
        ),
        '排浦镇' => 
        array (
        ),
        '东成镇' => 
        array (
        ),
        '新州镇' => 
        array (
        ),
        '洋浦经济开发区' => 
        array (
        ),
        '华南热作学院' => 
        array (
        ),
      ),
      '省直辖县级行政区划' => 
      array (
        '五指山市' => 
        array (
        ),
        '琼海市' => 
        array (
        ),
        '文昌市' => 
        array (
        ),
        '万宁市' => 
        array (
        ),
        '东方市' => 
        array (
        ),
        '定安县' => 
        array (
        ),
        '屯昌县' => 
        array (
        ),
        '澄迈县' => 
        array (
        ),
        '临高县' => 
        array (
        ),
        '白沙黎族自治县' => 
        array (
        ),
        '昌江黎族自治县' => 
        array (
        ),
        '乐东黎族自治县' => 
        array (
        ),
        '陵水黎族自治县' => 
        array (
        ),
        '保亭黎族苗族自治县' => 
        array (
        ),
        '琼中黎族苗族自治县' => 
        array (
        ),
      ),
    ),
    '重庆' => 
    array (
      '万州区' => 
      array (
      ),
      '涪陵区' => 
      array (
      ),
      '渝中区' => 
      array (
      ),
      '大渡口区' => 
      array (
      ),
      '江北区' => 
      array (
      ),
      '沙坪坝区' => 
      array (
      ),
      '九龙坡区' => 
      array (
      ),
      '南岸区' => 
      array (
      ),
      '北碚区' => 
      array (
      ),
      '綦江区' => 
      array (
      ),
      '大足区' => 
      array (
      ),
      '渝北区' => 
      array (
      ),
      '巴南区' => 
      array (
      ),
      '黔江区' => 
      array (
      ),
      '长寿区' => 
      array (
      ),
      '江津区' => 
      array (
      ),
      '合川区' => 
      array (
      ),
      '永川区' => 
      array (
      ),
      '南川区' => 
      array (
      ),
      '璧山区' => 
      array (
      ),
      '铜梁区' => 
      array (
      ),
      '潼南区' => 
      array (
      ),
      '荣昌区' => 
      array (
      ),
      '开州区' => 
      array (
      ),
      '梁平区' => 
      array (
      ),
      '武隆区' => 
      array (
      ),
      '城口县' => 
      array (
      ),
      '丰都县' => 
      array (
      ),
      '垫江县' => 
      array (
      ),
      '忠县' => 
      array (
      ),
      '云阳县' => 
      array (
      ),
      '奉节县' => 
      array (
      ),
      '巫山县' => 
      array (
      ),
      '巫溪县' => 
      array (
      ),
      '石柱土家族自治县' => 
      array (
      ),
      '秀山土家族苗族自治县' => 
      array (
      ),
      '酉阳土家族苗族自治县' => 
      array (
      ),
      '彭水苗族土家族自治县' => 
      array (
      ),
    ),
    '四川' => 
    array (
      '成都' => 
      array (
        '锦江区' => 
        array (
        ),
        '青羊区' => 
        array (
        ),
        '金牛区' => 
        array (
        ),
        '武侯区' => 
        array (
        ),
        '成华区' => 
        array (
        ),
        '龙泉驿区' => 
        array (
        ),
        '青白江区' => 
        array (
        ),
        '新都区' => 
        array (
        ),
        '温江区' => 
        array (
        ),
        '双流区' => 
        array (
        ),
        '郫都区' => 
        array (
        ),
        '新津区' => 
        array (
        ),
        '金堂县' => 
        array (
        ),
        '大邑县' => 
        array (
        ),
        '蒲江县' => 
        array (
        ),
        '都江堰市' => 
        array (
        ),
        '彭州市' => 
        array (
        ),
        '邛崃市' => 
        array (
        ),
        '崇州市' => 
        array (
        ),
        '简阳市' => 
        array (
        ),
      ),
      '自贡' => 
      array (
        '自流井区' => 
        array (
        ),
        '贡井区' => 
        array (
        ),
        '大安区' => 
        array (
        ),
        '沿滩区' => 
        array (
        ),
        '荣县' => 
        array (
        ),
        '富顺县' => 
        array (
        ),
      ),
      '攀枝花' => 
      array (
        '东区' => 
        array (
        ),
        '西区' => 
        array (
        ),
        '仁和区' => 
        array (
        ),
        '米易县' => 
        array (
        ),
        '盐边县' => 
        array (
        ),
      ),
      '泸州' => 
      array (
        '江阳区' => 
        array (
        ),
        '纳溪区' => 
        array (
        ),
        '龙马潭区' => 
        array (
        ),
        '泸县' => 
        array (
        ),
        '合江县' => 
        array (
        ),
        '叙永县' => 
        array (
        ),
        '古蔺县' => 
        array (
        ),
      ),
      '德阳' => 
      array (
        '旌阳区' => 
        array (
        ),
        '罗江区' => 
        array (
        ),
        '中江县' => 
        array (
        ),
        '广汉市' => 
        array (
        ),
        '什邡市' => 
        array (
        ),
        '绵竹市' => 
        array (
        ),
      ),
      '绵阳' => 
      array (
        '涪城区' => 
        array (
        ),
        '游仙区' => 
        array (
        ),
        '安州区' => 
        array (
        ),
        '三台县' => 
        array (
        ),
        '盐亭县' => 
        array (
        ),
        '梓潼县' => 
        array (
        ),
        '北川羌族自治县' => 
        array (
        ),
        '平武县' => 
        array (
        ),
        '江油市' => 
        array (
        ),
      ),
      '广元' => 
      array (
        '利州区' => 
        array (
        ),
        '昭化区' => 
        array (
        ),
        '朝天区' => 
        array (
        ),
        '旺苍县' => 
        array (
        ),
        '青川县' => 
        array (
        ),
        '剑阁县' => 
        array (
        ),
        '苍溪县' => 
        array (
        ),
      ),
      '遂宁' => 
      array (
        '船山区' => 
        array (
        ),
        '安居区' => 
        array (
        ),
        '蓬溪县' => 
        array (
        ),
        '大英县' => 
        array (
        ),
        '射洪市' => 
        array (
        ),
      ),
      '内江' => 
      array (
        '市中区' => 
        array (
        ),
        '东兴区' => 
        array (
        ),
        '威远县' => 
        array (
        ),
        '资中县' => 
        array (
        ),
        '隆昌市' => 
        array (
        ),
      ),
      '乐山' => 
      array (
        '市中区' => 
        array (
        ),
        '沙湾区' => 
        array (
        ),
        '五通桥区' => 
        array (
        ),
        '金口河区' => 
        array (
        ),
        '犍为县' => 
        array (
        ),
        '井研县' => 
        array (
        ),
        '夹江县' => 
        array (
        ),
        '沐川县' => 
        array (
        ),
        '峨边彝族自治县' => 
        array (
        ),
        '马边彝族自治县' => 
        array (
        ),
        '峨眉山市' => 
        array (
        ),
      ),
      '南充' => 
      array (
        '顺庆区' => 
        array (
        ),
        '高坪区' => 
        array (
        ),
        '嘉陵区' => 
        array (
        ),
        '南部县' => 
        array (
        ),
        '营山县' => 
        array (
        ),
        '蓬安县' => 
        array (
        ),
        '仪陇县' => 
        array (
        ),
        '西充县' => 
        array (
        ),
        '阆中市' => 
        array (
        ),
      ),
      '眉山' => 
      array (
        '东坡区' => 
        array (
        ),
        '彭山区' => 
        array (
        ),
        '仁寿县' => 
        array (
        ),
        '洪雅县' => 
        array (
        ),
        '丹棱县' => 
        array (
        ),
        '青神县' => 
        array (
        ),
      ),
      '宜宾' => 
      array (
        '翠屏区' => 
        array (
        ),
        '南溪区' => 
        array (
        ),
        '叙州区' => 
        array (
        ),
        '江安县' => 
        array (
        ),
        '长宁县' => 
        array (
        ),
        '高县' => 
        array (
        ),
        '珙县' => 
        array (
        ),
        '筠连县' => 
        array (
        ),
        '兴文县' => 
        array (
        ),
        '屏山县' => 
        array (
        ),
      ),
      '广安' => 
      array (
        '广安区' => 
        array (
        ),
        '前锋区' => 
        array (
        ),
        '岳池县' => 
        array (
        ),
        '武胜县' => 
        array (
        ),
        '邻水县' => 
        array (
        ),
        '华蓥市' => 
        array (
        ),
      ),
      '达州' => 
      array (
        '通川区' => 
        array (
        ),
        '达川区' => 
        array (
        ),
        '宣汉县' => 
        array (
        ),
        '开江县' => 
        array (
        ),
        '大竹县' => 
        array (
        ),
        '渠县' => 
        array (
        ),
        '万源市' => 
        array (
        ),
      ),
      '雅安' => 
      array (
        '雨城区' => 
        array (
        ),
        '名山区' => 
        array (
        ),
        '荥经县' => 
        array (
        ),
        '汉源县' => 
        array (
        ),
        '石棉县' => 
        array (
        ),
        '天全县' => 
        array (
        ),
        '芦山县' => 
        array (
        ),
        '宝兴县' => 
        array (
        ),
      ),
      '巴中' => 
      array (
        '巴州区' => 
        array (
        ),
        '恩阳区' => 
        array (
        ),
        '通江县' => 
        array (
        ),
        '南江县' => 
        array (
        ),
        '平昌县' => 
        array (
        ),
      ),
      '资阳' => 
      array (
        '雁江区' => 
        array (
        ),
        '安岳县' => 
        array (
        ),
        '乐至县' => 
        array (
        ),
      ),
      '阿坝藏族羌族自治州' => 
      array (
        '马尔康市' => 
        array (
        ),
        '汶川县' => 
        array (
        ),
        '理县' => 
        array (
        ),
        '茂县' => 
        array (
        ),
        '松潘县' => 
        array (
        ),
        '九寨沟县' => 
        array (
        ),
        '金川县' => 
        array (
        ),
        '小金县' => 
        array (
        ),
        '黑水县' => 
        array (
        ),
        '壤塘县' => 
        array (
        ),
        '阿坝县' => 
        array (
        ),
        '若尔盖县' => 
        array (
        ),
        '红原县' => 
        array (
        ),
      ),
      '甘孜藏族自治州' => 
      array (
        '康定市' => 
        array (
        ),
        '泸定县' => 
        array (
        ),
        '丹巴县' => 
        array (
        ),
        '九龙县' => 
        array (
        ),
        '雅江县' => 
        array (
        ),
        '道孚县' => 
        array (
        ),
        '炉霍县' => 
        array (
        ),
        '甘孜县' => 
        array (
        ),
        '新龙县' => 
        array (
        ),
        '德格县' => 
        array (
        ),
        '白玉县' => 
        array (
        ),
        '石渠县' => 
        array (
        ),
        '色达县' => 
        array (
        ),
        '理塘县' => 
        array (
        ),
        '巴塘县' => 
        array (
        ),
        '乡城县' => 
        array (
        ),
        '稻城县' => 
        array (
        ),
        '得荣县' => 
        array (
        ),
      ),
      '凉山彝族自治州' => 
      array (
        '西昌市' => 
        array (
        ),
        '会理市' => 
        array (
        ),
        '木里藏族自治县' => 
        array (
        ),
        '盐源县' => 
        array (
        ),
        '德昌县' => 
        array (
        ),
        '会东县' => 
        array (
        ),
        '宁南县' => 
        array (
        ),
        '普格县' => 
        array (
        ),
        '布拖县' => 
        array (
        ),
        '金阳县' => 
        array (
        ),
        '昭觉县' => 
        array (
        ),
        '喜德县' => 
        array (
        ),
        '冕宁县' => 
        array (
        ),
        '越西县' => 
        array (
        ),
        '甘洛县' => 
        array (
        ),
        '美姑县' => 
        array (
        ),
        '雷波县' => 
        array (
        ),
      ),
    ),
    '贵州' => 
    array (
      '贵阳' => 
      array (
        '南明区' => 
        array (
        ),
        '云岩区' => 
        array (
        ),
        '花溪区' => 
        array (
        ),
        '乌当区' => 
        array (
        ),
        '白云区' => 
        array (
        ),
        '观山湖区' => 
        array (
        ),
        '开阳县' => 
        array (
        ),
        '息烽县' => 
        array (
        ),
        '修文县' => 
        array (
        ),
        '清镇市' => 
        array (
        ),
      ),
      '六盘水' => 
      array (
        '钟山区' => 
        array (
        ),
        '六枝特区' => 
        array (
        ),
        '水城区' => 
        array (
        ),
        '盘州市' => 
        array (
        ),
      ),
      '遵义' => 
      array (
        '红花岗区' => 
        array (
        ),
        '汇川区' => 
        array (
        ),
        '播州区' => 
        array (
        ),
        '桐梓县' => 
        array (
        ),
        '绥阳县' => 
        array (
        ),
        '正安县' => 
        array (
        ),
        '道真仡佬族苗族自治县' => 
        array (
        ),
        '务川仡佬族苗族自治县' => 
        array (
        ),
        '凤冈县' => 
        array (
        ),
        '湄潭县' => 
        array (
        ),
        '余庆县' => 
        array (
        ),
        '习水县' => 
        array (
        ),
        '赤水市' => 
        array (
        ),
        '仁怀市' => 
        array (
        ),
      ),
      '安顺' => 
      array (
        '西秀区' => 
        array (
        ),
        '平坝区' => 
        array (
        ),
        '普定县' => 
        array (
        ),
        '镇宁布依族苗族自治县' => 
        array (
        ),
        '关岭布依族苗族自治县' => 
        array (
        ),
        '紫云苗族布依族自治县' => 
        array (
        ),
      ),
      '毕节' => 
      array (
        '七星关区' => 
        array (
        ),
        '大方县' => 
        array (
        ),
        '金沙县' => 
        array (
        ),
        '织金县' => 
        array (
        ),
        '纳雍县' => 
        array (
        ),
        '威宁彝族回族苗族自治县' => 
        array (
        ),
        '赫章县' => 
        array (
        ),
        '黔西市' => 
        array (
        ),
      ),
      '铜仁' => 
      array (
        '碧江区' => 
        array (
        ),
        '万山区' => 
        array (
        ),
        '江口县' => 
        array (
        ),
        '玉屏侗族自治县' => 
        array (
        ),
        '石阡县' => 
        array (
        ),
        '思南县' => 
        array (
        ),
        '印江土家族苗族自治县' => 
        array (
        ),
        '德江县' => 
        array (
        ),
        '沿河土家族自治县' => 
        array (
        ),
        '松桃苗族自治县' => 
        array (
        ),
      ),
      '黔西南布依族苗族自治州' => 
      array (
        '兴义市' => 
        array (
        ),
        '兴仁市' => 
        array (
        ),
        '普安县' => 
        array (
        ),
        '晴隆县' => 
        array (
        ),
        '贞丰县' => 
        array (
        ),
        '望谟县' => 
        array (
        ),
        '册亨县' => 
        array (
        ),
        '安龙县' => 
        array (
        ),
      ),
      '黔东南苗族侗族自治州' => 
      array (
        '凯里市' => 
        array (
        ),
        '黄平县' => 
        array (
        ),
        '施秉县' => 
        array (
        ),
        '三穗县' => 
        array (
        ),
        '镇远县' => 
        array (
        ),
        '岑巩县' => 
        array (
        ),
        '天柱县' => 
        array (
        ),
        '锦屏县' => 
        array (
        ),
        '剑河县' => 
        array (
        ),
        '台江县' => 
        array (
        ),
        '黎平县' => 
        array (
        ),
        '榕江县' => 
        array (
        ),
        '从江县' => 
        array (
        ),
        '雷山县' => 
        array (
        ),
        '麻江县' => 
        array (
        ),
        '丹寨县' => 
        array (
        ),
      ),
      '黔南布依族苗族自治州' => 
      array (
        '都匀市' => 
        array (
        ),
        '福泉市' => 
        array (
        ),
        '荔波县' => 
        array (
        ),
        '贵定县' => 
        array (
        ),
        '瓮安县' => 
        array (
        ),
        '独山县' => 
        array (
        ),
        '平塘县' => 
        array (
        ),
        '罗甸县' => 
        array (
        ),
        '长顺县' => 
        array (
        ),
        '龙里县' => 
        array (
        ),
        '惠水县' => 
        array (
        ),
        '三都水族自治县' => 
        array (
        ),
      ),
    ),
    '云南' => 
    array (
      '昆明' => 
      array (
        '五华区' => 
        array (
        ),
        '盘龙区' => 
        array (
        ),
        '官渡区' => 
        array (
        ),
        '西山区' => 
        array (
        ),
        '东川区' => 
        array (
        ),
        '呈贡区' => 
        array (
        ),
        '晋宁区' => 
        array (
        ),
        '富民县' => 
        array (
        ),
        '宜良县' => 
        array (
        ),
        '石林彝族自治县' => 
        array (
        ),
        '嵩明县' => 
        array (
        ),
        '禄劝彝族苗族自治县' => 
        array (
        ),
        '寻甸回族彝族自治县' => 
        array (
        ),
        '安宁市' => 
        array (
        ),
      ),
      '曲靖' => 
      array (
        '麒麟区' => 
        array (
        ),
        '沾益区' => 
        array (
        ),
        '马龙区' => 
        array (
        ),
        '陆良县' => 
        array (
        ),
        '师宗县' => 
        array (
        ),
        '罗平县' => 
        array (
        ),
        '富源县' => 
        array (
        ),
        '会泽县' => 
        array (
        ),
        '宣威市' => 
        array (
        ),
      ),
      '玉溪' => 
      array (
        '红塔区' => 
        array (
        ),
        '江川区' => 
        array (
        ),
        '通海县' => 
        array (
        ),
        '华宁县' => 
        array (
        ),
        '易门县' => 
        array (
        ),
        '峨山彝族自治县' => 
        array (
        ),
        '新平彝族傣族自治县' => 
        array (
        ),
        '元江哈尼族彝族傣族自治县' => 
        array (
        ),
        '澄江市' => 
        array (
        ),
      ),
      '保山' => 
      array (
        '隆阳区' => 
        array (
        ),
        '施甸县' => 
        array (
        ),
        '龙陵县' => 
        array (
        ),
        '昌宁县' => 
        array (
        ),
        '腾冲市' => 
        array (
        ),
      ),
      '昭通' => 
      array (
        '昭阳区' => 
        array (
        ),
        '鲁甸县' => 
        array (
        ),
        '巧家县' => 
        array (
        ),
        '盐津县' => 
        array (
        ),
        '大关县' => 
        array (
        ),
        '永善县' => 
        array (
        ),
        '绥江县' => 
        array (
        ),
        '镇雄县' => 
        array (
        ),
        '彝良县' => 
        array (
        ),
        '威信县' => 
        array (
        ),
        '水富市' => 
        array (
        ),
      ),
      '丽江' => 
      array (
        '古城区' => 
        array (
        ),
        '玉龙纳西族自治县' => 
        array (
        ),
        '永胜县' => 
        array (
        ),
        '华坪县' => 
        array (
        ),
        '宁蒗彝族自治县' => 
        array (
        ),
      ),
      '普洱' => 
      array (
        '思茅区' => 
        array (
        ),
        '宁洱哈尼族彝族自治县' => 
        array (
        ),
        '墨江哈尼族自治县' => 
        array (
        ),
        '景东彝族自治县' => 
        array (
        ),
        '景谷傣族彝族自治县' => 
        array (
        ),
        '镇沅彝族哈尼族拉祜族自治县' => 
        array (
        ),
        '江城哈尼族彝族自治县' => 
        array (
        ),
        '孟连傣族拉祜族佤族自治县' => 
        array (
        ),
        '澜沧拉祜族自治县' => 
        array (
        ),
        '西盟佤族自治县' => 
        array (
        ),
      ),
      '临沧' => 
      array (
        '临翔区' => 
        array (
        ),
        '凤庆县' => 
        array (
        ),
        '云县' => 
        array (
        ),
        '永德县' => 
        array (
        ),
        '镇康县' => 
        array (
        ),
        '双江拉祜族佤族布朗族傣族自治县' => 
        array (
        ),
        '耿马傣族佤族自治县' => 
        array (
        ),
        '沧源佤族自治县' => 
        array (
        ),
      ),
      '楚雄彝族自治州' => 
      array (
        '楚雄市' => 
        array (
        ),
        '禄丰市' => 
        array (
        ),
        '双柏县' => 
        array (
        ),
        '牟定县' => 
        array (
        ),
        '南华县' => 
        array (
        ),
        '姚安县' => 
        array (
        ),
        '大姚县' => 
        array (
        ),
        '永仁县' => 
        array (
        ),
        '元谋县' => 
        array (
        ),
        '武定县' => 
        array (
        ),
      ),
      '红河哈尼族彝族自治州' => 
      array (
        '个旧市' => 
        array (
        ),
        '开远市' => 
        array (
        ),
        '蒙自市' => 
        array (
        ),
        '弥勒市' => 
        array (
        ),
        '屏边苗族自治县' => 
        array (
        ),
        '建水县' => 
        array (
        ),
        '石屏县' => 
        array (
        ),
        '泸西县' => 
        array (
        ),
        '元阳县' => 
        array (
        ),
        '红河县' => 
        array (
        ),
        '金平苗族瑶族傣族自治县' => 
        array (
        ),
        '绿春县' => 
        array (
        ),
        '河口瑶族自治县' => 
        array (
        ),
      ),
      '文山壮族苗族自治州' => 
      array (
        '文山市' => 
        array (
        ),
        '砚山县' => 
        array (
        ),
        '西畴县' => 
        array (
        ),
        '麻栗坡县' => 
        array (
        ),
        '马关县' => 
        array (
        ),
        '丘北县' => 
        array (
        ),
        '广南县' => 
        array (
        ),
        '富宁县' => 
        array (
        ),
      ),
      '西双版纳傣族自治州' => 
      array (
        '景洪市' => 
        array (
        ),
        '勐海县' => 
        array (
        ),
        '勐腊县' => 
        array (
        ),
      ),
      '大理白族自治州' => 
      array (
        '大理市' => 
        array (
        ),
        '漾濞彝族自治县' => 
        array (
        ),
        '祥云县' => 
        array (
        ),
        '宾川县' => 
        array (
        ),
        '弥渡县' => 
        array (
        ),
        '南涧彝族自治县' => 
        array (
        ),
        '巍山彝族回族自治县' => 
        array (
        ),
        '永平县' => 
        array (
        ),
        '云龙县' => 
        array (
        ),
        '洱源县' => 
        array (
        ),
        '剑川县' => 
        array (
        ),
        '鹤庆县' => 
        array (
        ),
      ),
      '德宏傣族景颇族自治州' => 
      array (
        '瑞丽市' => 
        array (
        ),
        '芒市' => 
        array (
        ),
        '梁河县' => 
        array (
        ),
        '盈江县' => 
        array (
        ),
        '陇川县' => 
        array (
        ),
      ),
      '怒江傈僳族自治州' => 
      array (
        '泸水市' => 
        array (
        ),
        '福贡县' => 
        array (
        ),
        '贡山独龙族怒族自治县' => 
        array (
        ),
        '兰坪白族普米族自治县' => 
        array (
        ),
      ),
      '迪庆藏族自治州' => 
      array (
        '香格里拉市' => 
        array (
        ),
        '德钦县' => 
        array (
        ),
        '维西傈僳族自治县' => 
        array (
        ),
      ),
    ),
    '西藏' => 
    array (
      '拉萨' => 
      array (
        '城关区' => 
        array (
        ),
        '堆龙德庆区' => 
        array (
        ),
        '达孜区' => 
        array (
        ),
        '林周县' => 
        array (
        ),
        '当雄县' => 
        array (
        ),
        '尼木县' => 
        array (
        ),
        '曲水县' => 
        array (
        ),
        '墨竹工卡县' => 
        array (
        ),
        '格尔木藏青工业园区' => 
        array (
        ),
        '拉萨经济技术开发区' => 
        array (
        ),
        '西藏文化旅游创意园区' => 
        array (
        ),
        '达孜工业园区' => 
        array (
        ),
      ),
      '日喀则' => 
      array (
        '桑珠孜区' => 
        array (
        ),
        '南木林县' => 
        array (
        ),
        '江孜县' => 
        array (
        ),
        '定日县' => 
        array (
        ),
        '萨迦县' => 
        array (
        ),
        '拉孜县' => 
        array (
        ),
        '昂仁县' => 
        array (
        ),
        '谢通门县' => 
        array (
        ),
        '白朗县' => 
        array (
        ),
        '仁布县' => 
        array (
        ),
        '康马县' => 
        array (
        ),
        '定结县' => 
        array (
        ),
        '仲巴县' => 
        array (
        ),
        '亚东县' => 
        array (
        ),
        '吉隆县' => 
        array (
        ),
        '聂拉木县' => 
        array (
        ),
        '萨嘎县' => 
        array (
        ),
        '岗巴县' => 
        array (
        ),
      ),
      '昌都' => 
      array (
        '卡若区' => 
        array (
        ),
        '江达县' => 
        array (
        ),
        '贡觉县' => 
        array (
        ),
        '类乌齐县' => 
        array (
        ),
        '丁青县' => 
        array (
        ),
        '察雅县' => 
        array (
        ),
        '八宿县' => 
        array (
        ),
        '左贡县' => 
        array (
        ),
        '芒康县' => 
        array (
        ),
        '洛隆县' => 
        array (
        ),
        '边坝县' => 
        array (
        ),
      ),
      '林芝' => 
      array (
        '巴宜区' => 
        array (
        ),
        '工布江达县' => 
        array (
        ),
        '墨脱县' => 
        array (
        ),
        '波密县' => 
        array (
        ),
        '察隅县' => 
        array (
        ),
        '朗县' => 
        array (
        ),
        '米林市' => 
        array (
        ),
      ),
      '山南' => 
      array (
        '乃东区' => 
        array (
        ),
        '扎囊县' => 
        array (
        ),
        '贡嘎县' => 
        array (
        ),
        '桑日县' => 
        array (
        ),
        '琼结县' => 
        array (
        ),
        '曲松县' => 
        array (
        ),
        '措美县' => 
        array (
        ),
        '洛扎县' => 
        array (
        ),
        '加查县' => 
        array (
        ),
        '隆子县' => 
        array (
        ),
        '浪卡子县' => 
        array (
        ),
        '错那市' => 
        array (
        ),
      ),
      '那曲' => 
      array (
        '色尼区' => 
        array (
        ),
        '嘉黎县' => 
        array (
        ),
        '比如县' => 
        array (
        ),
        '聂荣县' => 
        array (
        ),
        '安多县' => 
        array (
        ),
        '申扎县' => 
        array (
        ),
        '索县' => 
        array (
        ),
        '班戈县' => 
        array (
        ),
        '巴青县' => 
        array (
        ),
        '尼玛县' => 
        array (
        ),
        '双湖县' => 
        array (
        ),
      ),
      '阿里地区' => 
      array (
        '普兰县' => 
        array (
        ),
        '札达县' => 
        array (
        ),
        '噶尔县' => 
        array (
        ),
        '日土县' => 
        array (
        ),
        '革吉县' => 
        array (
        ),
        '改则县' => 
        array (
        ),
        '措勤县' => 
        array (
        ),
      ),
    ),
    '陕西' => 
    array (
      '西安' => 
      array (
        '新城区' => 
        array (
        ),
        '碑林区' => 
        array (
        ),
        '莲湖区' => 
        array (
        ),
        '灞桥区' => 
        array (
        ),
        '未央区' => 
        array (
        ),
        '雁塔区' => 
        array (
        ),
        '阎良区' => 
        array (
        ),
        '临潼区' => 
        array (
        ),
        '长安区' => 
        array (
        ),
        '高陵区' => 
        array (
        ),
        '鄠邑区' => 
        array (
        ),
        '蓝田县' => 
        array (
        ),
        '周至县' => 
        array (
        ),
      ),
      '铜川' => 
      array (
        '王益区' => 
        array (
        ),
        '印台区' => 
        array (
        ),
        '耀州区' => 
        array (
        ),
        '宜君县' => 
        array (
        ),
      ),
      '宝鸡' => 
      array (
        '渭滨区' => 
        array (
        ),
        '金台区' => 
        array (
        ),
        '陈仓区' => 
        array (
        ),
        '凤翔区' => 
        array (
        ),
        '岐山县' => 
        array (
        ),
        '扶风县' => 
        array (
        ),
        '眉县' => 
        array (
        ),
        '陇县' => 
        array (
        ),
        '千阳县' => 
        array (
        ),
        '麟游县' => 
        array (
        ),
        '凤县' => 
        array (
        ),
        '太白县' => 
        array (
        ),
      ),
      '咸阳' => 
      array (
        '秦都区' => 
        array (
        ),
        '杨陵区' => 
        array (
        ),
        '渭城区' => 
        array (
        ),
        '三原县' => 
        array (
        ),
        '泾阳县' => 
        array (
        ),
        '乾县' => 
        array (
        ),
        '礼泉县' => 
        array (
        ),
        '永寿县' => 
        array (
        ),
        '长武县' => 
        array (
        ),
        '旬邑县' => 
        array (
        ),
        '淳化县' => 
        array (
        ),
        '武功县' => 
        array (
        ),
        '兴平市' => 
        array (
        ),
        '彬州市' => 
        array (
        ),
      ),
      '渭南' => 
      array (
        '临渭区' => 
        array (
        ),
        '华州区' => 
        array (
        ),
        '潼关县' => 
        array (
        ),
        '大荔县' => 
        array (
        ),
        '合阳县' => 
        array (
        ),
        '澄城县' => 
        array (
        ),
        '蒲城县' => 
        array (
        ),
        '白水县' => 
        array (
        ),
        '富平县' => 
        array (
        ),
        '韩城市' => 
        array (
        ),
        '华阴市' => 
        array (
        ),
      ),
      '延安' => 
      array (
        '宝塔区' => 
        array (
        ),
        '安塞区' => 
        array (
        ),
        '延长县' => 
        array (
        ),
        '延川县' => 
        array (
        ),
        '志丹县' => 
        array (
        ),
        '吴起县' => 
        array (
        ),
        '甘泉县' => 
        array (
        ),
        '富县' => 
        array (
        ),
        '洛川县' => 
        array (
        ),
        '宜川县' => 
        array (
        ),
        '黄龙县' => 
        array (
        ),
        '黄陵县' => 
        array (
        ),
        '子长市' => 
        array (
        ),
      ),
      '汉中' => 
      array (
        '汉台区' => 
        array (
        ),
        '南郑区' => 
        array (
        ),
        '城固县' => 
        array (
        ),
        '洋县' => 
        array (
        ),
        '西乡县' => 
        array (
        ),
        '勉县' => 
        array (
        ),
        '宁强县' => 
        array (
        ),
        '略阳县' => 
        array (
        ),
        '镇巴县' => 
        array (
        ),
        '留坝县' => 
        array (
        ),
        '佛坪县' => 
        array (
        ),
      ),
      '榆林' => 
      array (
        '榆阳区' => 
        array (
        ),
        '横山区' => 
        array (
        ),
        '府谷县' => 
        array (
        ),
        '靖边县' => 
        array (
        ),
        '定边县' => 
        array (
        ),
        '绥德县' => 
        array (
        ),
        '米脂县' => 
        array (
        ),
        '佳县' => 
        array (
        ),
        '吴堡县' => 
        array (
        ),
        '清涧县' => 
        array (
        ),
        '子洲县' => 
        array (
        ),
        '神木市' => 
        array (
        ),
      ),
      '安康' => 
      array (
        '汉滨区' => 
        array (
        ),
        '汉阴县' => 
        array (
        ),
        '石泉县' => 
        array (
        ),
        '宁陕县' => 
        array (
        ),
        '紫阳县' => 
        array (
        ),
        '岚皋县' => 
        array (
        ),
        '平利县' => 
        array (
        ),
        '镇坪县' => 
        array (
        ),
        '白河县' => 
        array (
        ),
        '旬阳市' => 
        array (
        ),
      ),
      '商洛' => 
      array (
        '商州区' => 
        array (
        ),
        '洛南县' => 
        array (
        ),
        '丹凤县' => 
        array (
        ),
        '商南县' => 
        array (
        ),
        '山阳县' => 
        array (
        ),
        '镇安县' => 
        array (
        ),
        '柞水县' => 
        array (
        ),
      ),
    ),
    '甘肃' => 
    array (
      '兰州' => 
      array (
        '城关区' => 
        array (
        ),
        '七里河区' => 
        array (
        ),
        '西固区' => 
        array (
        ),
        '安宁区' => 
        array (
        ),
        '红古区' => 
        array (
        ),
        '永登县' => 
        array (
        ),
        '皋兰县' => 
        array (
        ),
        '榆中县' => 
        array (
        ),
        '兰州新区' => 
        array (
        ),
      ),
      '嘉峪关' => 
      array (
        '雄关街道' => 
        array (
        ),
        '钢城街道' => 
        array (
        ),
        '新城镇' => 
        array (
        ),
        '峪泉镇' => 
        array (
        ),
        '文殊镇' => 
        array (
        ),
      ),
      '金昌' => 
      array (
        '金川区' => 
        array (
        ),
        '永昌县' => 
        array (
        ),
      ),
      '白银' => 
      array (
        '白银区' => 
        array (
        ),
        '平川区' => 
        array (
        ),
        '靖远县' => 
        array (
        ),
        '会宁县' => 
        array (
        ),
        '景泰县' => 
        array (
        ),
      ),
      '天水' => 
      array (
        '秦州区' => 
        array (
        ),
        '麦积区' => 
        array (
        ),
        '清水县' => 
        array (
        ),
        '秦安县' => 
        array (
        ),
        '甘谷县' => 
        array (
        ),
        '武山县' => 
        array (
        ),
        '张家川回族自治县' => 
        array (
        ),
      ),
      '武威' => 
      array (
        '凉州区' => 
        array (
        ),
        '民勤县' => 
        array (
        ),
        '古浪县' => 
        array (
        ),
        '天祝藏族自治县' => 
        array (
        ),
      ),
      '张掖' => 
      array (
        '甘州区' => 
        array (
        ),
        '肃南裕固族自治县' => 
        array (
        ),
        '民乐县' => 
        array (
        ),
        '临泽县' => 
        array (
        ),
        '高台县' => 
        array (
        ),
        '山丹县' => 
        array (
        ),
      ),
      '平凉' => 
      array (
        '崆峒区' => 
        array (
        ),
        '泾川县' => 
        array (
        ),
        '灵台县' => 
        array (
        ),
        '崇信县' => 
        array (
        ),
        '庄浪县' => 
        array (
        ),
        '静宁县' => 
        array (
        ),
        '华亭市' => 
        array (
        ),
      ),
      '酒泉' => 
      array (
        '肃州区' => 
        array (
        ),
        '金塔县' => 
        array (
        ),
        '瓜州县' => 
        array (
        ),
        '肃北蒙古族自治县' => 
        array (
        ),
        '阿克塞哈萨克族自治县' => 
        array (
        ),
        '玉门市' => 
        array (
        ),
        '敦煌市' => 
        array (
        ),
      ),
      '庆阳' => 
      array (
        '西峰区' => 
        array (
        ),
        '庆城县' => 
        array (
        ),
        '环县' => 
        array (
        ),
        '华池县' => 
        array (
        ),
        '合水县' => 
        array (
        ),
        '正宁县' => 
        array (
        ),
        '宁县' => 
        array (
        ),
        '镇原县' => 
        array (
        ),
      ),
      '定西' => 
      array (
        '安定区' => 
        array (
        ),
        '通渭县' => 
        array (
        ),
        '陇西县' => 
        array (
        ),
        '渭源县' => 
        array (
        ),
        '临洮县' => 
        array (
        ),
        '漳县' => 
        array (
        ),
        '岷县' => 
        array (
        ),
      ),
      '陇南' => 
      array (
        '武都区' => 
        array (
        ),
        '成县' => 
        array (
        ),
        '文县' => 
        array (
        ),
        '宕昌县' => 
        array (
        ),
        '康县' => 
        array (
        ),
        '西和县' => 
        array (
        ),
        '礼县' => 
        array (
        ),
        '徽县' => 
        array (
        ),
        '两当县' => 
        array (
        ),
      ),
      '临夏回族自治州' => 
      array (
        '临夏市' => 
        array (
        ),
        '临夏县' => 
        array (
        ),
        '康乐县' => 
        array (
        ),
        '永靖县' => 
        array (
        ),
        '广河县' => 
        array (
        ),
        '和政县' => 
        array (
        ),
        '东乡族自治县' => 
        array (
        ),
        '积石山保安族东乡族撒拉族自治县' => 
        array (
        ),
      ),
      '甘南藏族自治州' => 
      array (
        '合作市' => 
        array (
        ),
        '临潭县' => 
        array (
        ),
        '卓尼县' => 
        array (
        ),
        '舟曲县' => 
        array (
        ),
        '迭部县' => 
        array (
        ),
        '玛曲县' => 
        array (
        ),
        '碌曲县' => 
        array (
        ),
        '夏河县' => 
        array (
        ),
      ),
    ),
    '青海' => 
    array (
      '西宁' => 
      array (
        '城东区' => 
        array (
        ),
        '城中区' => 
        array (
        ),
        '城西区' => 
        array (
        ),
        '城北区' => 
        array (
        ),
        '湟中区' => 
        array (
        ),
        '大通回族土族自治县' => 
        array (
        ),
        '湟源县' => 
        array (
        ),
      ),
      '海东' => 
      array (
        '乐都区' => 
        array (
        ),
        '平安区' => 
        array (
        ),
        '民和回族土族自治县' => 
        array (
        ),
        '互助土族自治县' => 
        array (
        ),
        '化隆回族自治县' => 
        array (
        ),
        '循化撒拉族自治县' => 
        array (
        ),
      ),
      '海北藏族自治州' => 
      array (
        '门源回族自治县' => 
        array (
        ),
        '祁连县' => 
        array (
        ),
        '海晏县' => 
        array (
        ),
        '刚察县' => 
        array (
        ),
      ),
      '黄南藏族自治州' => 
      array (
        '同仁市' => 
        array (
        ),
        '尖扎县' => 
        array (
        ),
        '泽库县' => 
        array (
        ),
        '河南蒙古族自治县' => 
        array (
        ),
      ),
      '海南藏族自治州' => 
      array (
        '共和县' => 
        array (
        ),
        '同德县' => 
        array (
        ),
        '贵德县' => 
        array (
        ),
        '兴海县' => 
        array (
        ),
        '贵南县' => 
        array (
        ),
      ),
      '果洛藏族自治州' => 
      array (
        '玛沁县' => 
        array (
        ),
        '班玛县' => 
        array (
        ),
        '甘德县' => 
        array (
        ),
        '达日县' => 
        array (
        ),
        '久治县' => 
        array (
        ),
        '玛多县' => 
        array (
        ),
      ),
      '玉树藏族自治州' => 
      array (
        '玉树市' => 
        array (
        ),
        '杂多县' => 
        array (
        ),
        '称多县' => 
        array (
        ),
        '治多县' => 
        array (
        ),
        '囊谦县' => 
        array (
        ),
        '曲麻莱县' => 
        array (
        ),
      ),
      '海西蒙古族藏族自治州' => 
      array (
        '格尔木市' => 
        array (
        ),
        '德令哈市' => 
        array (
        ),
        '茫崖市' => 
        array (
        ),
        '乌兰县' => 
        array (
        ),
        '都兰县' => 
        array (
        ),
        '天峻县' => 
        array (
        ),
        '大柴旦行政委员会' => 
        array (
        ),
      ),
    ),
    '宁夏' => 
    array (
      '银川' => 
      array (
        '兴庆区' => 
        array (
        ),
        '西夏区' => 
        array (
        ),
        '金凤区' => 
        array (
        ),
        '永宁县' => 
        array (
        ),
        '贺兰县' => 
        array (
        ),
        '灵武市' => 
        array (
        ),
      ),
      '石嘴山' => 
      array (
        '大武口区' => 
        array (
        ),
        '惠农区' => 
        array (
        ),
        '平罗县' => 
        array (
        ),
      ),
      '吴忠' => 
      array (
        '利通区' => 
        array (
        ),
        '红寺堡区' => 
        array (
        ),
        '盐池县' => 
        array (
        ),
        '同心县' => 
        array (
        ),
        '青铜峡市' => 
        array (
        ),
      ),
      '固原' => 
      array (
        '原州区' => 
        array (
        ),
        '西吉县' => 
        array (
        ),
        '隆德县' => 
        array (
        ),
        '泾源县' => 
        array (
        ),
        '彭阳县' => 
        array (
        ),
      ),
      '中卫' => 
      array (
        '沙坡头区' => 
        array (
        ),
        '中宁县' => 
        array (
        ),
        '海原县' => 
        array (
        ),
      ),
    ),
    '新疆' => 
    array (
      '乌鲁木齐' => 
      array (
        '天山区' => 
        array (
        ),
        '沙依巴克区' => 
        array (
        ),
        '新市区' => 
        array (
        ),
        '水磨沟区' => 
        array (
        ),
        '头屯河区' => 
        array (
        ),
        '达坂城区' => 
        array (
        ),
        '米东区' => 
        array (
        ),
        '乌鲁木齐县' => 
        array (
        ),
      ),
      '克拉玛依' => 
      array (
        '独山子区' => 
        array (
        ),
        '克拉玛依区' => 
        array (
        ),
        '白碱滩区' => 
        array (
        ),
        '乌尔禾区' => 
        array (
        ),
      ),
      '吐鲁番' => 
      array (
        '高昌区' => 
        array (
        ),
        '鄯善县' => 
        array (
        ),
        '托克逊县' => 
        array (
        ),
      ),
      '哈密' => 
      array (
        '伊州区' => 
        array (
        ),
        '巴里坤哈萨克自治县' => 
        array (
        ),
        '伊吾县' => 
        array (
        ),
      ),
      '昌吉回族自治州' => 
      array (
        '昌吉市' => 
        array (
        ),
        '阜康市' => 
        array (
        ),
        '呼图壁县' => 
        array (
        ),
        '玛纳斯县' => 
        array (
        ),
        '奇台县' => 
        array (
        ),
        '吉木萨尔县' => 
        array (
        ),
        '木垒哈萨克自治县' => 
        array (
        ),
      ),
      '博尔塔拉蒙古自治州' => 
      array (
        '博乐市' => 
        array (
        ),
        '阿拉山口市' => 
        array (
        ),
        '精河县' => 
        array (
        ),
        '温泉县' => 
        array (
        ),
      ),
      '巴音郭楞蒙古自治州' => 
      array (
        '库尔勒市' => 
        array (
        ),
        '轮台县' => 
        array (
        ),
        '尉犁县' => 
        array (
        ),
        '若羌县' => 
        array (
        ),
        '且末县' => 
        array (
        ),
        '焉耆回族自治县' => 
        array (
        ),
        '和静县' => 
        array (
        ),
        '和硕县' => 
        array (
        ),
        '博湖县' => 
        array (
        ),
      ),
      '阿克苏地区' => 
      array (
        '阿克苏市' => 
        array (
        ),
        '库车市' => 
        array (
        ),
        '温宿县' => 
        array (
        ),
        '沙雅县' => 
        array (
        ),
        '新和县' => 
        array (
        ),
        '拜城县' => 
        array (
        ),
        '乌什县' => 
        array (
        ),
        '阿瓦提县' => 
        array (
        ),
        '柯坪县' => 
        array (
        ),
      ),
      '克孜勒苏柯尔克孜自治州' => 
      array (
        '阿图什市' => 
        array (
        ),
        '阿克陶县' => 
        array (
        ),
        '阿合奇县' => 
        array (
        ),
        '乌恰县' => 
        array (
        ),
      ),
      '喀什地区' => 
      array (
        '喀什市' => 
        array (
        ),
        '疏附县' => 
        array (
        ),
        '疏勒县' => 
        array (
        ),
        '英吉沙县' => 
        array (
        ),
        '泽普县' => 
        array (
        ),
        '莎车县' => 
        array (
        ),
        '叶城县' => 
        array (
        ),
        '麦盖提县' => 
        array (
        ),
        '岳普湖县' => 
        array (
        ),
        '伽师县' => 
        array (
        ),
        '巴楚县' => 
        array (
        ),
        '塔什库尔干塔吉克自治县' => 
        array (
        ),
      ),
      '和田地区' => 
      array (
        '和田市' => 
        array (
        ),
        '和田县' => 
        array (
        ),
        '墨玉县' => 
        array (
        ),
        '皮山县' => 
        array (
        ),
        '洛浦县' => 
        array (
        ),
        '策勒县' => 
        array (
        ),
        '于田县' => 
        array (
        ),
        '民丰县' => 
        array (
        ),
      ),
      '伊犁哈萨克自治州' => 
      array (
        '伊宁市' => 
        array (
        ),
        '奎屯市' => 
        array (
        ),
        '霍尔果斯市' => 
        array (
        ),
        '伊宁县' => 
        array (
        ),
        '察布查尔锡伯自治县' => 
        array (
        ),
        '霍城县' => 
        array (
        ),
        '巩留县' => 
        array (
        ),
        '新源县' => 
        array (
        ),
        '昭苏县' => 
        array (
        ),
        '特克斯县' => 
        array (
        ),
        '尼勒克县' => 
        array (
        ),
      ),
      '塔城地区' => 
      array (
        '塔城市' => 
        array (
        ),
        '乌苏市' => 
        array (
        ),
        '沙湾市' => 
        array (
        ),
        '额敏县' => 
        array (
        ),
        '托里县' => 
        array (
        ),
        '裕民县' => 
        array (
        ),
        '和布克赛尔蒙古自治县' => 
        array (
        ),
      ),
      '阿勒泰地区' => 
      array (
        '阿勒泰市' => 
        array (
        ),
        '布尔津县' => 
        array (
        ),
        '富蕴县' => 
        array (
        ),
        '福海县' => 
        array (
        ),
        '哈巴河县' => 
        array (
        ),
        '青河县' => 
        array (
        ),
        '吉木乃县' => 
        array (
        ),
      ),
      '自治区直辖县级行政区划' => 
      array (
        '石河子市' => 
        array (
        ),
        '阿拉尔市' => 
        array (
        ),
        '图木舒克市' => 
        array (
        ),
        '五家渠市' => 
        array (
        ),
        '北屯市' => 
        array (
        ),
        '铁门关市' => 
        array (
        ),
        '双河市' => 
        array (
        ),
        '可克达拉市' => 
        array (
        ),
        '昆玉市' => 
        array (
        ),
        '胡杨河市' => 
        array (
        ),
        '新星市' => 
        array (
        ),
        '白杨市' => 
        array (
        ),
      ),
    ),
    '台湾' => 
    array (
      '台北' => 
      array (
      ),
      '高雄' => 
      array (
      ),
      '台中' => 
      array (
      ),
      '台南' => 
      array (
      ),
      '新北' => 
      array (
      ),
      '桃园' => 
      array (
      ),
    ),
    '香港' => 
    array (
      '香港' => 
      array (
      ),
    ),
    '澳门' => 
    array (
      '澳门' => 
      array (
      ),
    ),
  ),
  '美国' => 
  array (
    '加利福尼亚' => 
    array (
      '洛杉矶' => 
      array (
      ),
      '旧金山' => 
      array (
      ),
      '圣何塞' => 
      array (
      ),
    ),
    '纽约' => 
    array (
      '纽约' => 
      array (
      ),
    ),
    '得克萨斯' => 
    array (
      '休斯顿' => 
      array (
      ),
      '达拉斯' => 
      array (
      ),
    ),
    '华盛顿' => 
    array (
      '西雅图' => 
      array (
      ),
    ),
  ),
  '日本' => 
  array (
    '东京' => 
    array (
      '东京' => 
      array (
      ),
    ),
    '大阪' => 
    array (
      '大阪' => 
      array (
      ),
    ),
  ),
  '韩国' => 
  array (
    '首尔' => 
    array (
      '首尔' => 
      array (
      ),
    ),
  ),
  '新加坡' => 
  array (
    '新加坡' => 
    array (
      '新加坡' => 
      array (
      ),
    ),
  ),
  '德国' => 
  array (
    '柏林' => 
    array (
      '柏林' => 
      array (
      ),
    ),
    '黑森' => 
    array (
      '法兰克福' => 
      array (
      ),
    ),
  ),
  '英国' => 
  array (
    '伦敦' => 
    array (
      '伦敦' => 
      array (
      ),
    ),
  ),
);
