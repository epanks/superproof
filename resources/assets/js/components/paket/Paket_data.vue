<template>
  <div class="content">
    <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
      <div>
        <b-form-select v-model="abat" :options="tblabat"></b-form-select>
      </div>
      <!-- <div>
        <b-form-select v-model="kdoutput" :options="tblkdoutput" multiple :select-size="4"></b-form-select>
      </div>-->
      <div>
        <b-form-select
          @onchange="getTblkdoutput()"
          v-model="kdoutput"
          :options="tblkdoutput"
          type="kdoutput"
        ></b-form-select>
      </div>
      <div class="col-md-12">
        <div class="row mt-3">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Paket Table</h3>

              <div class="card-tools">
                <button class="btn btn-success" @click="newModal">
                  Add New
                  <i class="fas fa-user-plus fa-fw"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <tbody>
                  <tr>
                    <th>ID</th>
                    <th>Nama Paket</th>
                    <th>Pagu</th>
                    <th>Keuangan</th>
                    <th>Progres Keuangan</th>
                    <th>Progres Fisik</th>
                    <th>Modify</th>
                  </tr>

                  <tr v-for="paket in paketdt.data" :key="paket.id">
                    <td>{{paket.id}}</td>
                    <td>{{paket.nmpaket}}</td>
                    <td class="text-right">{{paket.pagurmp | numeral('0,0')}}</td>
                    <td class="text-right">{{paket.keuangan | numeral('0,0')}}</td>
                    <td class="text-right">{{paket.keuangan/paket.pagurmp*100 | numeral('0,0.00')}}</td>
                    <td class="text-right">{{paket.progres_fisik | numeral('0,0.00')}}</td>

                    <td>
                      <a href="#" @click="editModal(paket)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" @click="deletePaket(paket.id)">
                        <i class="fa fa-trash red"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <div class="overflow-auto">
                <pagination
                  class="mb-0"
                  :data="paketdt"
                  @pagination-change-page="getResults"
                  :limit="limit"
                  :show-disabled="showDisabled"
                  :size="size"
                  :align="align"
                />
              </div>
            </div>
          </div>
        </div>
        <!-- /.card -->
      </div>
    </div>

    <div v-if="!$gate.isAdminOrAuthor()">
      <not-found></not-found>
    </div>

    <!-- Modal -->
    <div
      class="modal fade"
      id="addNew"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addNewLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Add New</h5>
            <h5 class="modal-title" v-show="editmode" id="addNewLabel">Update Paket</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form @submit.prevent="editmode ? updatePaket() : createPaket()">
            <div class="modal-body">
              <div class="form-group">
                <input
                  v-model="form.nmpaket"
                  type="text"
                  name="nmpaket"
                  placeholder="Nama Paket"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('nmpaket') }"
                />
                <has-error :form="form" field="nmpaket"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.pagurmp"
                  type="number"
                  name="pagurmp"
                  placeholder="Pagu"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('pagurmp') }"
                />
                <has-error :form="form" field="pagurmp"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.keuangan"
                  type="number"
                  name="keuangan"
                  placeholder="Keuangan"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('keuangan') }"
                />
                <has-error :form="form" field="keuangan"></has-error>
              </div>

              <div class="form-group">
                <input
                  v-model="form.progres_fisik"
                  type="number"
                  name="progres_fisik"
                  placeholder="Pagu"
                  step="0.01"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.has('progres_fisik') }"
                />
                <has-error :form="form" field="progres_fisik"></has-error>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
              <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      limit: 2,
      showDisabled: false,
      size: "default",
      align: "left",
      editmode: false,
      paketdt: {},
      form: new Form({
        id: "",
        nmpaket: "",
        pagurmp: "",
        keuangan: "",
        progres_fisik: ""
      }),
      abat: null,
      tblabat: [
        { value: null, text: "----Please select----" },
        {
          value: "1",
          text: "Perencanaan"
        },
        { value: "2", text: "Air Tanah" },
        {
          value: "3",
          text: "Air Baku"
        },
        { value: "4", text: "Layanan Internal (Overhead)" },
        { value: "5", text: "Administrasi" }
      ],
      kdoutput: null,
      tblkdoutput: [
        { value: null, text: "----Please select----" },
        {
          value: "001",
          text:
            "Rencana teknis dan dokumen lingkungan hidup untuk konstruksi air tanah dan air baku"
        },
        { value: "010", text: "Sumur JIAT yang direhabilitasi" },
        {
          value: "011",
          text: "Jaringan irigasi air tanah (JIAT) yang direhabilitasi"
        },
        { value: "002", text: "Sumur air tanah untuk air baku yang dibangun" },
        { value: "003", text: "Embung air baku yang dibangun" },
        { value: "004", text: "Unit air baku yang dibangun" },
        { value: "005", text: "Sumur JIAT yang dibangun/ditingkatkan" },
        {
          value: "006",
          text: "Jaringan irigasi air tanah (JIAT) yang dibangun/ditingkatkan"
        },
        {
          value: "007",
          text: "Sumur air tanah untuk air baku yang direhabilitasi"
        },
        { value: "008", text: "Embung air baku yang direhabilitasi" },
        { value: "009", text: "Unit air baku yang direhabilitasi" },
        { value: "951", text: "Layanan Internal (Overhead)" },
        { value: "970", text: "Layanan Dukungan Manajemen Satker" }
      ]
    };
  },
  methods: {
    getResults(page = 1) {
      axios.get("api/paket?page=" + page).then(response => {
        this.paketdt = response.data;
      });
    },
    updatePaket() {
      this.$Progress.start();
      // console.log('Editing data');
      this.form
        .put("api/paket/" + this.form.id)
        .then(() => {
          // success
          $("#addNew").modal("hide");
          Swal.fire("Updated!", "Information has been updated.", "success");
          this.$Progress.finish();
          Fire.$emit("AfterCreate");
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    editModal(paket) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(paket);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    deletePaket(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then(result => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("api/paket/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been deleted.", "success");
              Fire.$emit("AfterCreate");
            })
            .catch(() => {
              Swal.fire("Failed!", "There was something wronge.", "warning");
            });
        }
      });
    },
    loadPaketdt() {
      if (this.$gate.isAdminOrAuthor()) {
        axios.get("api/paket").then(({ data }) => (this.paketdt = data));
      }
    },

    createPaket() {
      this.$Progress.start();

      this.form
        .post("api/paket")
        .then(() => {
          Fire.$emit("AfterCreate");
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: "User Created in successfully"
          });
          this.$Progress.finish();
        })
        .catch(() => {});
    }
  },
  created() {
    Fire.$on("searching", () => {
      let query = this.$parent.search;
      axios
        .get("api/findPaket?q=" + query)
        .then(data => {
          this.paketdt = data.data;
        })
        .catch(() => {});
    });
    this.loadPaketdt();
    Fire.$on("AfterCreate", () => {
      this.loadPaketdt();
    });
    //    setInterval(() => this.loadUsers(), 3000);
  }
};
</script>
