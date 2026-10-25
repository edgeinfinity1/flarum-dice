# 发布准备

[English](RELEASING.md) · [技术文档](TECHNICAL.zh-CN.md)

0.1.0 已进入公开仓库审阅，所有者仍要调整介绍。本阶段只上传仓库：**不建标签、GitHub Release，不提交 Packagist，不配置自动发布**。`dev-main` 为预览。

## 首次打标签前

1. 完成介绍，核对双语内容、截图链接、元数据与素材说明。
2. 校验 Composer 并在全新兼容 Flarum 2 实例安装；按条件验证 PHP 8.3／8.4、实体手机、Safari，记录未测组合。
3. 执行 PHP／JS 测试和 Node 24 干净构建，对比 bundles／maps 和翻译。仅在隔离站点检查创建、编辑、复用、删除、严格编辑、预览、摘要及停用／重装。
4. 确认 npm 版本、更新记录、Git 状态。Composer 不写固定 version；运行代码不得依赖留档目录。
5. 取得所有者明确授权后，才从审阅提交建立不可移动的 `v0.1.0` 标签与 GitHub Release。
6. 另行授权后提交 Packagist；如需自动发布，再配置加密仓库 Secrets，并核对入库后 P2 source／dist refs。

## 本地 path 安装

站点 path 版本覆盖不代表 Git 标签或公开发行。如推进安装版本，保持根要求、path 覆盖、lock、installed 一致，保留其它依赖及安装方式。不得用开发站整套 vendor、配置或数据库覆盖主站。

仓库 Checks 在推送及 PR 时运行 PHP 8.3／8.4 和 Node 24 测试／构建；权限只读，不含 Release 或 Packagist 操作。
