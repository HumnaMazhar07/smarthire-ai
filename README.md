# smarthire-ai
AI based recruitment system (PHP + Python + Ollama)
# 🚀 SmartHire AI – Intelligent Recruitment System

An AI-powered recruitment platform that automatically analyzes CVs, matches candidates with job descriptions, and generates intelligent hiring scores using a Python-based AI engine integrated with a PHP + MySQL backend.

---

## 📌 Overview

SmartHire AI is a full-stack recruitment system designed to modernize hiring by using Artificial Intelligence. It evaluates candidates based on:

- CV content analysis (PDF parsing)
- Job description matching
- Skill extraction
- AI-generated smart scoring (0–100)
- Recruiter decision support (Hire / Reject / Review)

---

## ✨ Features

### 👨‍💼 Candidate Side
- Apply for jobs online
- Upload CV (PDF/DOC/DOCX)
- Add cover letter
- Get AI-based application evaluation

### 🧑‍💼 Recruiter Panel
- Post new jobs
- Edit / delete job listings
- View applications
- Accept / reject candidates
- View AI smart scores

### 🤖 AI Engine
- Reads full CV using PDF parsing (PyMuPDF)
- Extracts skills automatically
- Compares CV with job description
- Generates intelligent match score (0–100)
- Provides AI reasoning output

---

## 🧠 AI Technology

The AI module uses:

- Python (CV processing)
- PyMuPDF (PDF text extraction)
- Local LLM integration (Ollama / Llama3 optional upgrade)
- Smart scoring algorithm (semantic + rule-based hybrid model)

---

## 🛠️ Tech Stack

| Layer        | Technology |
|--------------|------------|
| Frontend     | HTML, CSS, JavaScript |
| Backend      | PHP (Core PHP) |
| Database     | MySQL |
| AI Engine    | Python |
| PDF Parsing  | PyMuPDF |
| Server       | Apache (XAMPP) |

---

## Setup

### 1. Install dependencies
pip install pymupdf requests

### 2. Install Ollama

https://ollama.com
ollama run llama3

### 3. Import Database
Import `/db/database.sql`

### 4. Run project

http://localhost/smarthire

## AI Engine
- Uses local LLM (no paid API)
- Fully offline AI processing

## Author
Humna Mazhar
