### Arellano University Online Canteen Ordering System (Updated as of March 24, 2026)
 
> A full-stack web application that lets university students order canteen meals online, skip the queue, and pick up their food at a chosen time.
 
Built as a **Grade 12 research project** by a students of Arellano University.
 
---
 
## 📌 Table of Contents
 
- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Team](#team)
- [License](#license)
 
---
 
## Overview
 
The **Arellano Online Canteen Ordering System** replaces the traditional walk-up canteen experience with a modern, browser-based ordering platform. Students log in, browse the daily menu, add items to their cart, select a pickup time, and place their order, all in without standing in line.
 
Canteen administrators get a dedicated dashboard to manage food items, monitor incoming orders, and view daily sales summaries in real time.
 
---
 
## Features
 
### Student Side
- **Authentication** — Secure register and login with role-based access (student / admin)
- **Menu Browsing** — Browse food items with category filters and search
- **Cart** — Add, update, and remove items; persisted in the backend
- **Checkout** — Select a pickup time slot and add special instructions
- **Order History** — View past orders and their current status
- **Order Cancellation** — Cancel a pending order before preparation begins
 
### Admin Side
- **Admin Dashboard** — Clean, dedicated panel with sidebar navigation
- **Manage Food** — Add, edit, and delete menu items with availability toggle
- **Manage Orders** — View all orders, filter by status, and update order progress
- **Daily Orders** — Summary of today's orders with total revenue count
 
---
 
## Tech Stack
 
| Layer | Technology |
|---|---|
| **Frontend** | React 18, Redux Toolkit, React Router v6 |
| **Backend** | Node.js, Express.js |
| **Database** | Supabase (PostgreSQL) |
| **HTTP Client** | Axios (with request/response interceptors) |
| **File Upload** | Multer |
| **Notifications** | React Toastify |
 
---
 
### Installation
 
**1. Clone the repository**
```bash
git clone https://github.com/ILikeSaltedEgg/AU-Canteen-System.git
cd AU-Canteen-System
```
**2. Set up the backend**
```bash
cd backend
npm install
```
 
Start the backend:
```bash
npm run dev
```
 
**3. Set up the frontend**
```bash
cd frontend
npm install
```
Start the frontend:
```bash
npm start
```
 
The app will be available at `http://localhost:3000`.
 
---
 
## Team
 
| Name | Role |
|---|---|
| Calimlim, Ace Joshua A.| Full Stack Developer, UI/UX & Documentation,  Database & Testing |
| Tolentino, Ralp Laurence |
| Rosarito, Timothy M. |
| Martinez, Rafael Luis N. |
| Pacoma, Rissalyn |
| Sioson, Aldrin |
 
---
 
## License
 
This project was developed for **educational purposes** as part of a Grade 12 research requirement at Arellano University. Not intended for commercial use.
 
---
 
*Built with ❤️ by ILikeSaltedEgg — 2025*

