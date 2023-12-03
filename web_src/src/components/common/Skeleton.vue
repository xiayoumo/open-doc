<template>
  <div class="skeleton-box">
    <el-skeleton-item variant="image" class="skeleton-image"/>
    <div class="skeleton-content">
      <el-skeleton :rows="contentRow" animated />
    </div>
  </div>
</template>

<script>
export default {
  name: "Skeleton",
  props: {
    contentRow: {
      type: Number,
      default: 15
    }
  }
}
</script>

<style scoped lang="scss">
@import '~@/components/common/base.scss';

.skeleton-image{
  width: 240px;
  height: 240px;
}
.skeleton-content{
  padding-top: 14px;
}
.skeleton-box{
  padding: 14px;
}
</style>
