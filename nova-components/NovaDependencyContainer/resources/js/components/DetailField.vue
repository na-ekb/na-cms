<template>
    <div v-if="dependenciesSatisfied">
        <span v-for="childField in field.fields">
            <component
                :is="'detail-' + childField.component"
                :resource-id="resourceId"
                :resource-name="resourceName"
                :field="childField"
                :ref="'field-' + childField.attribute"
            />
        </span>
    </div>
</template>

<script>
	export default {
		props: ['resource', 'resourceName', 'resourceId', 'field'],

		created() {
			this.updateDependencyStatus()
		},

		data() {
			return {
				dependenciesSatisfied: false,
			}
		},

		methods: {

			updateDependencyStatus() {
				for (let dependency of this.field.dependencies) {
					if(dependency.satisfied) {
						this.dependenciesSatisfied = true;
						return;
					}
				}

				this.dependenciesSatisfied = false;
			},

		}
	}
</script>
