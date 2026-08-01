<script setup>
definePageMeta({
    middleware: [
        function (to, from) {
            return navigateTo('/guru/walas/dashboard')
        }
    ]
})
</script>
