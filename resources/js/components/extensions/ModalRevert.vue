<template>
  <div class="modal fade" id="modal-undo-win" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="exampleModalLongTitle">Revert Fight Winner</h5>
        </div>
        <div class="modal-body">
          <div class="form-group mt-2">
            <label>Fight #</label>
            <input type="number" class="form-control disabled" id="fight_no" />
          </div>
          <div class="revert-btn-group">
            <div class="form-group mt-2">
              <button @click="revertFight('M')" class="btn btn-danger btn-sm">
                MERON </button>
            </div>
            <div class="form-group mt-2">
              <button @click="revertFight('W')" class="btn btn-primary btn-sm">
                WALA </button>
            </div>
            <div class="form-group mt-2">
              <button @click="revertFight('D')" class="btn btn-warning btn-sm">
                DRAW </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      result: null,
    }
  },
  methods: {
    revertFight() {
      alert(result)
    }
  }
}

</script>