<template>
    <el-dialog 
        :visible.sync="visible" 
        :show-close="false" 
        custom-class="vocabulary-dialog" 
        @closed="closed">
        <div 
            slot="title" 
            class="title">
            <span class="icon-32 icon-vocabulary-blue"></span>
            <div v-if="reference">{{ reference.word.text }}</div>
        </div>
        <div 
            v-if="reference" 
            class="body">
            <div class="definition">
                {{ reference.word.description }}
            </div>
            <div 
                v-if="reference.word.image"
                class="image">
                <img 
                    :src="reference.word.image" 
                    alt="" />
            </div>
            <div class="samples">
                <div 
                    v-if="sentence"
                    class="audio">
                    <vocabulary-speaker :sentence="sentence" />
                    <span class="text">{{ reference.text }}</span>
                </div>
                <div 
                    v-if="reference.translation"
                    class="translation">
                    <flag-icon 
                        :country="flag"
                        size="normal"/>
                    <span class="text">{{ reference.translation }}</span>
                </div>
            </div>
        </div>
        <el-button 
            slot="footer" 
            type="warning" 
            @click="visible = false">{{ __('Ok') }}</el-button>
    </el-dialog>
</template>

<script>
import translation from '../../../services/translation';

export default {
    props: {
        reference: Object,
    },
    data() {
        return {
            visible: false,
            sentence: null,
        };
    },
    computed: {
        flag() {
            return translation.current || translation.earthFlag;
        },
    },
    watch: {
        reference(ref) {
            if (ref) {
                this.visible = true;
                this.sentence = ref.sentence;
            }
        },
    },
    methods: {
        closed() {
            this.$emit('closed');
        },
    },
};
</script>