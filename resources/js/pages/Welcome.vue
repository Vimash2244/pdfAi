<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="PDFParseAI - Extract Data from PDFs with AI">
        <meta name="description" content="Transform your PDFs into actionable data with our advanced AI-powered parsing technology. Extract text, tables, forms, and structured data automatically." />
    </Head>
    
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Navigation -->
        <nav class="relative z-50 px-6 py-4 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">PDFParseAI</span>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Features</a>
                        <a href="#how-it-works" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">How it Works</a>
                        <a href="#pricing" class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors">Pricing</a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <template v-if="$page.props.auth.user">
                <Link
                    :href="route('dashboard')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors"
                >
                    Dashboard
                </Link>
                        </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                                class="text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                                Get Started
                    </Link>
                </template>
                    </div>
                </div>
            </div>
            </nav>

        <!-- Hero Section -->
        <section class="relative px-6 py-20 lg:px-8 lg:py-32">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-5xl xl:text-6xl dark:text-white">
                                Extract Data from PDFs with
                                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">AI Magic</span>
                            </h1>
                            <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                                Transform complex PDF documents into structured, searchable data in seconds. Our advanced AI technology understands context, extracts tables, forms, and text with remarkable accuracy.
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <Link
                                :href="route('register')"
                                class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1"
                            >
                                Start Free Trial
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                            </Link>
                            <a
                                href="#demo"
                                class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-gray-700 bg-white border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:text-blue-600 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:border-blue-400 dark:hover:text-blue-400 transition-all duration-200"
                            >
                                Watch Demo
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>No credit card required</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>Free tier available</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <div class="relative z-10 rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-800">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="h-3 w-3 rounded-full bg-red-500"></div>
                                <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                                <div class="h-3 w-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="space-y-4">
                                <div class="h-4 bg-gray-200 rounded dark:bg-gray-700"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4 dark:bg-gray-700"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2 dark:bg-gray-700"></div>
                                <div class="grid grid-cols-3 gap-4 mt-6">
                                    <div class="h-20 bg-blue-100 rounded-lg dark:bg-blue-900/20"></div>
                                    <div class="h-20 bg-green-100 rounded-lg dark:bg-green-900/20"></div>
                                    <div class="h-20 bg-purple-100 rounded-lg dark:bg-purple-900/20"></div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 h-32 w-32 rounded-full bg-gradient-to-r from-blue-400 to-indigo-400 opacity-20 blur-2xl"></div>
                        <div class="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 opacity-20 blur-2xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="px-6 py-20 lg:px-8 lg:py-32 bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl dark:text-white">
                        Powerful Features for Every Use Case
                    </h2>
                    <p class="mt-6 text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        From simple text extraction to complex table parsing, our AI handles it all with precision and speed.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div class="group relative p-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl hover:from-blue-100 hover:to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 transition-all duration-300">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Smart Text Extraction</h3>
                        <p class="text-gray-600 dark:text-gray-300">AI-powered text recognition that maintains formatting, preserves structure, and handles multiple languages with high accuracy.</p>
                    </div>
                    
                    <div class="group relative p-8 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl hover:from-green-100 hover:to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 dark:hover:from-green-900/30 dark:hover:to-emerald-900/30 transition-all duration-300">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Table & Form Parsing</h3>
                        <p class="text-gray-600 dark:text-gray-300">Automatically detect and extract tables, forms, and structured data while maintaining relationships and formatting.</p>
                    </div>
                    
                    <div class="group relative p-8 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl hover:from-purple-100 hover:to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 dark:hover:from-purple-900/30 dark:hover:to-pink-900/30 transition-all duration-300">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Lightning Fast</h3>
                        <p class="text-gray-600 dark:text-gray-300">Process hundreds of pages in seconds with our optimized AI engine. Get results faster than ever before.</p>
                    </div>
                    
                    <div class="group relative p-8 bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl hover:from-orange-100 hover:to-red-100 dark:from-orange-900/20 dark:to-red-900/20 dark:hover:from-orange-900/30 dark:hover:to-red-900/30 transition-all duration-300">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-r from-orange-600 to-red-600 mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Secure & Private</h3>
                        <p class="text-gray-600 dark:text-gray-300">Enterprise-grade security with end-to-end encryption. Your documents are processed securely and never stored permanently.</p>
                    </div>
                    
                    <div class="group relative p-8 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl hover:from-teal-100 hover:to-cyan-100 dark:from-teal-900/20 dark:to-cyan-900/20 dark:hover:from-teal-900/30 dark:hover:to-cyan-900/30 transition-all duration-300">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-r from-teal-600 to-cyan-600 mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">API Integration</h3>
                        <p class="text-gray-600 dark:text-gray-300">RESTful API for seamless integration with your existing workflows, applications, and automation tools.</p>
                    </div>
                    
                    <div class="group relative p-8 bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl hover:from-yellow-100 hover:to-amber-100 dark:from-yellow-900/20 dark:to-amber-900/20 dark:hover:from-yellow-900/30 dark:hover:to-amber-900/30 transition-all duration-300">
                        <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-r from-yellow-600 to-amber-600 mb-6 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Custom Models</h3>
                        <p class="text-gray-600 dark:text-gray-300">Train custom AI models for your specific document types and industry requirements for even better accuracy.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="px-6 py-20 lg:px-8 lg:py-32 bg-gray-50 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl dark:text-white">
                        How It Works
                    </h2>
                    <p class="mt-6 text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                        Three simple steps to transform your PDFs into actionable data
                    </p>
                </div>
                
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-20 w-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-2xl font-bold">
                            1
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Upload Your PDF</h3>
                        <p class="text-gray-600 dark:text-gray-300">Simply drag and drop your PDF file or select it from your device. We support all PDF formats and sizes.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="flex items-center justify-center h-20 w-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-2xl font-bold">
                            2
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">AI Processing</h3>
                        <p class="text-gray-600 dark:text-gray-300">Our advanced AI analyzes your document, identifies structure, and extracts data with high precision and speed.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="flex items-center justify-center h-20 w-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 text-white text-2xl font-bold">
                            3
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 dark:text-white">Get Results</h3>
                        <p class="text-gray-600 dark:text-gray-300">Download your structured data in multiple formats: JSON, CSV, Excel, or integrate directly via our API.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="px-6 py-20 lg:px-8 lg:py-32 bg-gradient-to-r from-blue-600 to-indigo-600">
            <div class="mx-auto max-w-7xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl mb-6">
                    Ready to Transform Your PDFs?
                </h2>
                <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Join thousands of users who are already saving hours every day with our AI-powered PDF parsing technology.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-blue-600 bg-white rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1"
                    >
                        Start Free Trial
                        <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    </Link>
                    <a
                        href="#contact"
                        class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white border-2 border-white rounded-xl hover:bg-white hover:text-blue-600 transition-all duration-200"
                    >
                        Contact Sales
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white px-6 py-12 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                            </div>
                            <span class="text-xl font-bold">PDFParseAI</span>
                        </div>
                        <p class="text-gray-400 mb-4 max-w-md">
                            Transform your PDFs into actionable data with our advanced AI technology. Fast, accurate, and secure.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-4">Product</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                            <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                            <li><a href="#api" class="hover:text-white transition-colors">API</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-4">Company</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#about" class="hover:text-white transition-colors">About</a></li>
                            <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
                            <li><a href="#support" class="hover:text-white transition-colors">Support</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 PDFParseAI. All rights reserved.</p>
                </div>
        </div>
        </footer>
    </div>
</template>
