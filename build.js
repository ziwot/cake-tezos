#!/usr/bin/env node
import { build } from "esbuild";
import { polyfillNode } from "esbuild-plugin-polyfill-node";

await build({
	entryPoints: ["./assets/main.js"],
	bundle: true,
	outfile: "./webroot/dist/cake-tezos.js",
	minify: true,
	conditions: ["browser"],
	format: "esm",
	target: "esnext",
	plugins: [polyfillNode({})],
});
