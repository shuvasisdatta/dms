<template>
    <object :data="src" type="application/pdf" width="100%" height="700px">
        <p>Your Browser doesn't support PDF Viewer, Please Download the PDF from <a :href="src" download>here</a></p>
    </object>
</template>

<script>
export default {
    props: ['src'],
}
</script>