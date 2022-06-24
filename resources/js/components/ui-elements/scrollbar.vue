<script>
import ScrollBar from 'vue2-scrollbar';
export default {
    extends: ScrollBar,
    props: {
        speed: {
            type: Number,
            default: 25,
        },
    },
};
</script>