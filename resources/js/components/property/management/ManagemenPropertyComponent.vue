<template>
    <Dialog
        v-model:visible="visible"
        modal
        class="dialog-management"
        :style="{ width: '40rem' }"
        :draggable="false"
    >
        <template #header>
            <h3>Management Property</h3>
        </template>
        <div class="custom-form">
            <div class="custom-form-column">
                <FloatLabel>
                    <InputText
                        id="name"
                        class="inputtext-custom"
                        :class="{ 'p-invalid': errors.name }"
                        v-model="formAliado.name"
                        @input="clearError('name')"
                    />
                    <label for="name">Name</label>
                </FloatLabel>
                <small v-if="errors.name" class="p-error">{{
                    errors.name
                }}</small>
            </div>
        </div>
        <div class="custom-form">
            <div class="custom-form-column">
                <FloatLabel>
                    <InputText
                        id="address"
                        class="inputtext-custom"
                        :class="{ 'p-invalid': errors.address }"
                        v-model="formAliado.address"
                        @input="clearError('address')"
                    />
                    <label for="address">Address</label>
                </FloatLabel>
                <small v-if="errors.address" class="p-error">{{
                    errors.address
                }}</small>
            </div>
        </div>
        <div class="custom-form">
            <div class="custom-form-column">
                <Select
                    :options="listPropertyType"
                    v-model="formAliado.property_type_id"
                    placeholder="Select property type"
                    :class="{ 'p-invalid': errors.property_type_id }"
                    optionLabel="name"
                    optionValue="id"
                    style="width: 100%"
                />
                <small v-if="errors.property_type_id" class="p-error">{{
                    errors.property_type_id
                }}</small>
            </div>
        </div>
        <div class="custom-form">
            <div class="custom-form-column">
                <Select
                    :options="listStatus"
                    v-model="formAliado.status"
                    placeholder="Select status"
                    :class="{ 'p-invalid': errors.status }"
                    optionLabel="name"
                    optionValue="id"
                    style="width: 100%"
                />
                <small v-if="errors.status" class="p-error">{{
                    errors.status
                }}</small>
            </div>
        </div>
        <div class="custom-form">
            <div class="custom-form-column">
                <MultiSelect
                    :options="listOwners"
                    v-model="formAliado.owners"
                    filter
                    placeholder="Select owners"
                    :class="{ 'p-invalid': errors.owners }"
                    :maxSelectedLabels="limitOwners"
                    optionLabel="name"
                    optionValue="id"
                    style="width: 100%"
                />
                <small v-if="errors.owners" class="p-error">{{
                    errors.owners
                }}</small>
            </div>
        </div>
        <div class="custom-form">
            <div class="custom-form-column">
                <MultiSelect
                    :options="listTenants"
                    v-model="formAliado.tenants"
                    filter
                    placeholder="Select tenants"
                    :class="{ 'p-invalid': errors.tenants }"
                    :maxSelectedLabels="limitTenants"
                    optionLabel="name"
                    optionValue="id"
                    style="width: 100%"
                />
                <small v-if="errors.tenants" class="p-error">{{
                    errors.tenants
                }}</small>
            </div>
        </div>
        <hr />
        <template #footer>
            <div class="text-center">
                <Button
                    v-if="!selectedProperty"
                    label="Save"
                    severity="success"
                    style="margin-right: 10px"
                    @click="save"
                />
                <Button
                    v-else
                    label="Update"
                    severity="success"
                    style="margin-right: 10px"
                    @click="update"
                />
                <Button
                    label="Cancel"
                    severity="danger"
                    @click="handleDialogClose"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import * as Yup from "yup";
import MultiSelect from "primevue/multiselect";
import FloatLabel from "primevue/floatlabel";
import InputText from "primevue/inputtext";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import Button from "primevue/button";

export default {
    props: ["dialogVisible", "selectedProperty"],
    data() {
        return {
            visible: this.dialogVisible,
            formAliado: {
                id: null,
                name: null,
                address: null,
                property_type_id: null,
                status: null,
                owners: [],
                tenants: [],
            },
            errors: {},
            listPropertyType: [],
            listStatus: [],
            listOwners: [],
            listTenants: [],
            // limite de seleccion
            limitOwners: 10,
            limitTenants: 10,
        };
    },
    components: {
        FloatLabel,
        Dialog,
        Select,
        InputText,
        Button,
        MultiSelect,
    },
    watch: {},
    mounted() {
        this.$nextTick(() => {
            if (this.selectedProperty) {
                const currentOwnersName = this.$parseTags(
                    this.selectedProperty.owners_name
                );
                const currentTenantsName = this.$parseTags(
                    this.selectedProperty.tenants_name
                );
                this.formAliado.id = this.selectedProperty.id;
                this.formAliado.name = this.selectedProperty.name;
                this.formAliado.address = this.selectedProperty.address;
                this.formAliado.property_type_id =
                    this.selectedProperty.property_type_id;
                this.formAliado.status = parseInt(this.selectedProperty.status);
                this.formAliado.owners = currentOwnersName.map(
                    (owner) => owner.id
                );
                this.formAliado.tenants = currentTenantsName.map(
                    (tenat) => tenat.id
                );
            }
        });
    },
    created() {
        this.initServices();
    },
    methods: {
        async initServices() {
            this.listStatus = [
                { id: 1, name: "active" },
                { id: 2, name: "inactive" },
            ];
            const comboNames = ["property_type"];
            const response = await this.$getEnumsOptions(comboNames);
            const { property_type: responPropertyType } = response.data;
            this.listPropertyType = responPropertyType;
            // obtenemos los usuarios
            const { data: owners } = await this.getUsers("owners");
            this.listOwners = owners;
            const { data: tenants } = await this.getUsers("tenants");
            this.listTenants = tenants;
        },
        getUsers(role = null) {
            const vm = this;
            return new Promise((resolve, reject) => {
                const params = role ? { params: { role } } : {};
                this.$axios
                    .get(`/users/list`, params)
                    .then(function (response) {
                        resolve(response.data);
                    })
                    .catch((error) => {
                        vm.$readStatusHttp(error);
                        reject(error);
                    });
            });
        },
        async validateForm() {
            let initialRules = {
                name: Yup.string().required("Type name is required"),
                address: Yup.string().required("Type address is required"),
                status: Yup.string().required("Type owners is required"),
                owners: Yup.array()
                    .of(Yup.string().required("Each owner must be a string"))
                    .min(1, "At least one owner is required")
                    .required("Owners array is required"),
                tenants: Yup.array()
                    .of(Yup.string().required("Each tenant must be a string"))
                    .min(1, "At least one tenant is required")
                    .required("Tenants array is required"),
                property_type_id: Yup.string().required(
                    "Type property is required"
                ),
            };
            const schema = Yup.object().shape({
                ...initialRules,
            });
            this.errors = {};
            try {
                await schema.validate(this.formAliado, {
                    abortEarly: false,
                });
                return true;
            } catch (err) {
                err.inner.forEach((error) => {
                    this.errors[error.path] = error.message;
                });
                return false;
            }
        },
        async save() {
            const isValid = await this.validateForm();
            if (isValid) {
                this.$axios
                    .post("/properties/store", this.formAliado, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        this.$alertSuccess("Record Added");
                        this.$emit("reload", true);
                    })
                    .catch((error) => {
                        this.$readStatusHttp(error);
                    });
            }
        },
        async update() {
            const isValid = await this.validateForm();
            if (isValid) {
                this.$axios
                    .post(
                        "/properties/update/" + this.selectedProperty.id,
                        this.formAliado,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                        }
                    )
                    .then((response) => {
                        this.$alertSuccess("Register Updated");
                        this.$emit("reload", true);
                    })
                    .catch((error) => {
                        this.$readStatusHttp(error);
                    });
            }
        },
        handleDialogClose() {
            this.visible = false;
            this.$emit("hidden", this.visible);
        },
        clearError(field) {
            this.errors[field] = "";
        },
    },
};
</script>

<style>
#attachPhoto [data-pc-name="pcuploadbutton"] {
    display: none;
}
</style>
