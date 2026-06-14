import fitz
import sys
import json
import requests

cv_path = sys.argv[1]
job_text = sys.argv[2]

pdf = fitz.open(cv_path)

cv_text = ""
for page in pdf:
    cv_text += page.get_text()

cv_text = cv_text[:8000]

prompt = f"""
You are an AI recruitment engine.

Analyze CV vs Job Description and return ONLY valid JSON:

{{
  "score": 0-100,
  "verdict": "",
  "strengths": [],
  "weaknesses": [],
  "reasoning": ""
}}

CV:
{cv_text}

JOB:
{job_text}
"""

response = requests.post(
    "http://localhost:11434/api/generate",
    json={
        "model": "llama3",
        "prompt": prompt,
        "stream": False
    }
)

output = response.json()["response"]

try:
    result = json.loads(output)
except:
    result = {
        "score": 0,
        "verdict": "AI parsing error",
        "reasoning": output
    }

print(json.dumps(result, indent=2))
