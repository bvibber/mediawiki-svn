default:
	@echo "platform frag: $(PLAT_FRAG)"
	@ln -sf frag/$(PLAT_FRAG) plat_frag.mk
	@echo ""
	@echo "ok; ready to build."
