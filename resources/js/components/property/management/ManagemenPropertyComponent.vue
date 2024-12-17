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
                email: null,
            },
            errors: {},
            listPropertyType: [],
            listStatus: [],
        };
    },
    components: {
        FloatLabel,
        Dialog,
        Select,
        InputText,
        Button,
    },
    watch: {},
    mounted() {
        this.$nextTick(() => {
            if (this.selectedProperty) {
                this.formAliado.id = this.selectedProperty.id;
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
        },
        async validateForm() {
            let initialRules = {
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
                    .post("/users/store", this.formAliado, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        if (response.data.data == "EMAIL_NO") {
                            this.$alertWarning("Correo no existe");
                        } else {
                            this.$alertSuccess("Aliado Añadido");
                            this.$emit("reload", true);
                        }
                    })
                    .catch((error) => {
                        this.$readStatusHttp(error);
                    });
            }
        },
        async update() {
            const isValid = await this.validateForm();
            if (isValid) {
                this.formAliado.code_country = tmpFormatCountry;
                this.$axios
                    .post(
                        "/users/update/" + this.selectedProperty.id,
                        this.formAliado,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                        }
                    )
                    .then((response) => {
                        this.$alertSuccess("Aliado Actualizado");
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
