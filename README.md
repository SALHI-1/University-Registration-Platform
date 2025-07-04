# 🎓 University Online Admission Platform

This is a web-based system developed in **PHP**, **MySQL**, and **TailwindCSS** that allows students to apply online for university programs (Master or Engineering Cycle) based on their academic level (Bac+2 / Bac+3). The platform includes secure registration with email verification, application submission with document upload, and an administrative interface for processing and managing applications.

---

## 🚀 Main Features

### 🧑‍🎓 Student Area
- Register with email verification
- Apply for:
  - **Master or Engineering Cycle** (Bac+3 students)
  - **Engineering Cycle** only (Bac+2 students)
- Choose among multiple specializations (called *filières*)
- Upload necessary documents (transcripts, ID, etc.)
- Download a personalized **PDF pre-registration receipt**

### 🧑‍💼 Filial Coordinator (Chef de Filière)
- Reviews and filters applications for their assigned *filière*
- Accepts or rejects candidates based on eligibility
- Sets a limit on the number of students to admit
- System auto-generates an **Excel file** with the top candidates based on their academic grades

### 🛠️ Administrator Dashboard
- Manage and monitor all specializations (*filières*)
- View statistics about candidates and applications
- Create and manage **filial coordinators**

---

## 🗃️ Database Overview

The system includes the following main tables:

- **student**: personal info, email verification
- **candidature**: student applications with grades and uploaded documents
- **filiere**: program choice (Master or Engineering), linked to candidature
- **chef_filiere**: login and roles for filière coordinators
- **admin**: stored inside the same table as `chef_filiere` with role = 'admin'

The SQL dump is provided in the file [`University_DB.sql`](University_DB.sql).

---

## 🛠️ Tech Stack

- **Backend**: PHP (vanilla)
- **Database**: MySQL
- **Frontend**: Tailwind CSS

---

## 👤 Author
**Mohammed Salhi** - [Send me an email](https://mail.google.com/mail/?view=cm&fs=1&to=mohammedsalhisam@gmail.com&su=Inquiry%20about%20University%20Online%20Admission%20Platform)
